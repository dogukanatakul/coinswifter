<?php

namespace App\Jobs;

use App\Models\OrderTransaction;
use App\Models\Order;
use App\Models\Commission;
use App\Models\Parity;
use App\Models\ParityCommission;
use App\Models\UserCoin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class Exchange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $timeout = 300;
    public int $tries = 1;
    public array $logs = [];
    public array $nodLogs = [];
    public ?\Ramsey\Uuid\UuidInterface $uuid = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->logs = [];
        $this->nodLogs = [];
        $this->uuid = Uuid::uuid4();
    }

    public static function keepMonitorOnSuccess(): bool
    {
        return false;
    }


    /**
     * Execute the job.
     *
     * @return bool
     * @throws \Exception
     */


    public function market($buy): bool
    {
        try {
            $sells = Order::with(['parity.source', 'parity.coin', 'user'])
                ->where('amount', '>', 0)
                ->where('parities_id', $buy->parities_id)
                ->where('process', 'sell')
                ->orderBy('price', 'ASC')
                ->orderBy('microtime', 'ASC')
                ->limit(50)
                ->get();
            foreach ($sells as $sell) {
                if ($this->order($buy, $sell)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            report($e);
            printf("market ---" . $e->getMessage() . "\n\r");
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }


    public function limit($buy): bool
    {
        try {
            $sells = Order::with(['parity.source', 'parity.coin', 'user'])
                ->where('amount', '>', 0)
                ->where('parities_id', $buy->parities_id)
                ->where('process', 'sell')
                ->where('price', '<=', $buy->price)
                ->orderBy('price', 'ASC')
                ->orderBy('microtime', 'ASC')
                ->limit(50)
                ->get();
            foreach ($sells as $sell) {
                if ($this->order($buy, $sell)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            report($e);
            printf("limit ---" . $e->getMessage() . "\n\r");
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }


    /**
     * @throws \Exception
     */
    public function order($buy, $sell): bool
    {
        if ($buy->amount <= 0) {
            $this->logs[] = "Satın alma emiri bitmiş";
            return false; // satın alma bitmiş ama bot güncellenmemiş
        }
        if (empty($parity = Parity::find($buy->parities_id))) {
            $this->logs[] = "Parite çifti bulunamadı";
            return false;
        }
        // market fiyatından satış kontrolü
        if ($sell->price == 0) {
            $lastPrice = OrderTransaction::select('price', 'amount')
                ->where('parities_id', $buy->parities_id)
                ->where('price', '>', 0)
                ->orderBy('id', 'DESC')
                ->first();
            if (empty($lastPrice)) {
                $this->logs[] = "Aktif son fiyat bulunamadı!";
                return false; // son fiyat yok
            }
            $lastPrice = floatval($lastPrice->price);

        } else {
            $lastPrice = $sell->price;
        }
        $this->logs[] = "Aktif son fiyat " . $lastPrice . " olarak ayarlandı.";
        //\ market fiyatından satış kontrolü

        if (empty($parityCommission = ParityCommission::where('parities_id', $buy->parities_id)->first())) {
            $this->logs[] = "Komisyon tanımlanması bulunamadı.";
            return false; // komisyon tanımlaması yapılmamış
        }

        // alan kişi market fiyatından almak istiyorsa hesabından kesilen tutar kadar ücret öder.
        if ($buy->type === "market") {
            $buyAmount = $buy->total / $lastPrice;
        } else {
            $buyAmount = $buy->amount;
        }

        $this->logs[] = "Almak istenen amount: " . $buy->amount;
        $this->logs[] = "Satmak istenen amount: " . $sell->amount;


        DB::beginTransaction();
        $this->logs[] = "beginTransaction";
        //\
        if (($total = (floatval($buyAmount) - floatval($sell->amount))) > 0) {
            $this->logs[] = "Alanın emir miktarı satandan büyük";
            $totalAmount = $sell->amount;
            try {
                $buy->update([
                    'amount' => $total,
                    'total' => $total * $lastPrice
                ]);
                $this->logs[] = "Alanın emri " . $total . " olarak güncellendi";
                $sell->update([
                    'amount' => 0,
                    'total' => 0
                ]);
                $sell->delete();
            } catch (\Exception $e) {
                report($e);
                $this->logs[] = "RollBack";
                DB::rollBack();
                $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
                throw new \Exception($e);
            }

            $this->logs[] = "Satanın emri kapatıldı";
        } else if (($total = (floatval($sell->amount) - floatval($buyAmount))) > 0) {
            $this->logs[] = "Satanın emir amountı alandan büyük";
            $totalAmount = $buyAmount;
            try {
                $sell->update([
                    'amount' => $total,
                    'total' => $total * $lastPrice
                ]);
                $buy->update([
                    'amount' => 0,
                    'total' => 0
                ]);
                $buy->delete();
            } catch (\Exception $e) {
                report($e);
                $this->logs[] = "RollBack";
                DB::rollBack();
                $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
                throw new \Exception($e);
            }
            $this->logs[] = "Satanın emri " . $total . " olarak güncellendi";
            $this->logs[] = "Alanın emri kapatıldı";
        } else {
            $this->logs[] = "Emir amountları eşit";
            $totalAmount = $sell->amount;
            try {
                $sell->update([
                    'amount' => 0,
                    'total' => 0
                ]);
                $sell->delete();
                $buy->update([
                    'amount' => 0,
                    'total' => 0
                ]);
                $buy->delete();
                $this->logs[] = "Alanın emri kapatıldı.";
                $this->logs[] = "Satanında emri kapatıldı.";
            } catch (\Exception $e) {
                report($e);
                $this->logs[] = "RollBack";
                DB::rollBack();
                $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
                throw new \Exception($e);
            }
        }


        $lastCalc = [];
        $lastCalc["buy_commission"] = floatval(($totalAmount / 100) * floatval($parityCommission->commission));
        $lastCalc["buy"] = floatval($totalAmount - $lastCalc['buy_commission']);
        $lastCalc['buy_total'] = floatval($lastCalc['buy_commission'] + $lastCalc['buy']);
        $lastCalc['sell_commission'] = floatval((($totalAmount * $lastPrice) / 100) * floatval($parityCommission->commission));
        $lastCalc['sell'] = floatval(($totalAmount * $lastPrice)) - $lastCalc['sell_commission'];
        $lastCalc['sell_total'] = floatval($lastCalc['sell_commission'] + $lastCalc['sell']);


        $this->logs[] = "Hesaplar: " . implode("|", $lastCalc);


        // Alıcıya Ödeme Gelecek Cüzdan
        $buyWallet = UserCoin::with('coin')
            ->where('users_id', $buy->users_id)
            ->where('coins_id', $parity->coin_id)
            ->first();
        // Alıcının Ödeyeceği Cüzdan
        $buySourceWallet = UserCoin::with('coin')
            ->where('users_id', $buy->users_id)
            ->where('coins_id', $parity->source_coin_id)
            ->first();
        // Satıcıya Ödeme Gelecek Cüzdan
        $sellWallet = UserCoin::with('coin')
            ->where('users_id', $sell->users_id)
            ->where('coins_id', $parity->source_coin_id)
            ->first();
        // Satıcının Ödeme Göndereceği Cüzdan
        $sellSourceWallet = UserCoin::with('coin')
            ->where('users_id', $sell->users_id)
            ->where('coins_id', $parity->coin_id)
            ->first();

        try {

            /*
             *  wallet : istediği
             *  sourceWallet : karşılığında verdiği
             */
            $buySourceWallet->balance = $buySourceWallet->balance - ($lastCalc['sell'] + $lastCalc['sell_commission']);
            $buySourceWallet->save();

            $buyWallet->balance = $buyWallet->balance + $lastCalc['buy'];
            $buyWallet->save();

            $sellSourceWallet->balance = $sellSourceWallet->balance - ($lastCalc['buy'] + $lastCalc['buy_commission']);
            $sellSourceWallet->save();

            $sellWallet->balance = $sellWallet->balance + $lastCalc['sell'];
            $sellWallet->save();

            // Alıcıya gelen para birimi komisyonu
            Commission::create([
                'parities_id' => $buy->parities_id,
                'users_id' => $buyWallet->users_id,
                'user_coins_id' => $buyWallet->id,
                'coins_id' => $buyWallet->coins_id,
                'amount' => $lastCalc['buy_commission'],
                'price' => $lastPrice,
            ]);
            //\
            // Satıcıya gelen para birimi komisyonu
            Commission::create([
                'parities_id' => $sell->parities_id,
                'users_id' => $sellWallet->users_id,
                'user_coins_id' => $sellWallet->id,
                'coins_id' => $sellWallet->coins_id,
                'amount' => $lastCalc['sell_commission'],
                'price' => $lastPrice,
            ]);
            //\

            OrderTransaction::create([
                'parities_id' => $buy->parities_id,
                'buyer_user_id' => $buy->users_id,
                'buyer_order_id' => $buy->id,
                'seller_user_id' => $sell->users_id,
                'seller_order_id' => $sell->id,
                'price' => $lastPrice,
                'amount' => ($lastCalc['buy'] + $lastCalc['buy_commission']),
                'type' => $buy->type,
                'microtime' => str_replace(".", "", microtime(true)),
            ]);
            $this->logs[] = "Commit.";
            DB::commit();
            return true;
        } catch (\Exception $e) {
            report($e);
            $this->logs[] = "RollBack";
            DB::rollBack();
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }


    /**
     * @throws \Exception
     */
    public function handle()
    {
        $types = ["market", "limit"];
        $buys = Order::with(['parity.source', 'parity.coin', 'user'])
            ->where('amount', '>', 0)
            ->where('process', 'buy')
            ->whereIn('type', $types)
            ->orderBy('price', 'DESC')
            ->orderBy('microtime', 'ASC')
            ->get()
            ->groupBy('type');
        if (isset($buys['market']) && $buys['market']->count() > 0) {
            foreach ($buys['market'] as $buy) {
                if ($this->market($buy)) {
                    $this->queueData(['status' => 'success', 'message' => '']);
                    $this->printLog();
                    return true;
                }
            }
        }
        if (isset($buys['limit']) && $buys['limit']->count() > 0) {
            foreach ($buys['limit'] as $buy) {
                if ($this->limit($buy)) {
                    $this->queueData(['status' => 'success', 'message' => '']);
                    $this->printLog();
                    return true;
                }
            }
        }
        $this->printLog();
        $this->queueData(['status' => 'fail', 'message' => 'no_action']);
        return "islem_yok";
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }

    public function printLog()
    {
        if (PHP_SAPI !== 'cli') {
            printf(implode("\r\n<hr><br>", $this->logs));
        }
        return;
    }
}
