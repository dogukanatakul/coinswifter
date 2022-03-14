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
            $lastPrice = $lastPrice->price;

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
            $buyAmount = \Litipk\BigNumbers\Decimal::fromString($buy->total)->div(\Litipk\BigNumbers\Decimal::fromString($lastPrice), null)->innerValue();
        } else {
            $buyAmount = $buy->amount;
        }

        $this->logs[] = "Almak istenen amount: " . $buy->amount;
        $this->logs[] = "Satmak istenen amount: " . $sell->amount;


        $this->logs[] = "beginTransaction";
        //\
        if (($total = (\Litipk\BigNumbers\Decimal::fromString($buyAmount)->sub(\Litipk\BigNumbers\Decimal::fromString($sell->amount), null)->innerValue())) > 0) {
            $this->logs[] = "Alanın emir miktarı satandan büyük";
            $totalAmount = \Litipk\BigNumbers\Decimal::fromString($sell->amount)->innerValue();
            $buyUpdate = [
                'amount' => \Litipk\BigNumbers\Decimal::fromString($total)->innerValue(),
                'total' => \Litipk\BigNumbers\Decimal::fromString($total)->mul(\Litipk\BigNumbers\Decimal::fromString($lastPrice), null)->innerValue()
            ];
            $sellUpdate = [
                'amount' => 0,
                'total' => 0
            ];
            $this->logs[] = "Alanın emri " . $total . " olarak güncellendi";

            $this->logs[] = "Satanın emri kapatıldı";
        } else if (($total = (\Litipk\BigNumbers\Decimal::fromString($sell->amount)->sub(\Litipk\BigNumbers\Decimal::fromString($buyAmount), null)->innerValue())) > 0) {
            $this->logs[] = "Satanın emir amountı alandan büyük";
            $totalAmount = $buyAmount;
            $buyUpdate = [
                'amount' => 0,
                'total' => 0
            ];
            $sellUpdate = [
                'amount' => \Litipk\BigNumbers\Decimal::fromString($total)->innerValue(),
                'total' => \Litipk\BigNumbers\Decimal::fromString($total)->div(\Litipk\BigNumbers\Decimal::fromString($lastPrice), null)->innerValue(),
            ];
            $this->logs[] = "Satanın emri " . $total . " olarak güncellendi";
            $this->logs[] = "Alanın emri kapatıldı";
        } else {
            $this->logs[] = "Emir amountları eşit";
            $totalAmount = $sell->amount;
            $buyUpdate = [
                'amount' => 0,
                'total' => 0
            ];
            $sellUpdate = [
                'amount' => 0,
                'total' => 0
            ];
            $this->logs[] = "Alanın emri kapatıldı.";
            $this->logs[] = "Satanında emri kapatıldı.";
        }


        $lastCalc = [];
        $lastCalc["buy_commission"] = \Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($totalAmount)->div(\Litipk\BigNumbers\Decimal::fromString("100"), null)->innerValue())->mul(\Litipk\BigNumbers\Decimal::fromString($parityCommission->commission), null)->innerValue();
        $lastCalc["buy"] = \Litipk\BigNumbers\Decimal::fromString($totalAmount)->sub(\Litipk\BigNumbers\Decimal::fromString($lastCalc['buy_commission']), null)->innerValue();
        $lastCalc['buy_total'] = \Litipk\BigNumbers\Decimal::fromString($lastCalc['buy_commission'])->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['buy']), null)->innerValue();
        $lastCalc['sell_commission'] = \Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($totalAmount)->mul(\Litipk\BigNumbers\Decimal::fromString($lastPrice), null)->innerValue())->div(\Litipk\BigNumbers\Decimal::fromString("100"), null)->innerValue())->mul(\Litipk\BigNumbers\Decimal::fromString($parityCommission->commission), null)->innerValue();
        $lastCalc['sell'] = \Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($totalAmount)->mul(\Litipk\BigNumbers\Decimal::fromString($lastPrice), null)->innerValue())->sub(\Litipk\BigNumbers\Decimal::fromString($lastCalc['sell_commission']), null)->innerValue();
        $lastCalc['sell_total'] = \Litipk\BigNumbers\Decimal::fromString($lastCalc['sell_commission'])->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['sell']), null)->innerValue();


        $this->logs[] = "Hesaplar: " . implode("|", $lastCalc);

        DB::beginTransaction();
        try {
            /*
             *  wallet : istediği
             *  sourceWallet : karşılığında verdiği
             */
            // Alıcının Ödeyeceği Cüzdan
            $buySourceWallet = UserCoin::with('coin')
                ->where('users_id', $buy->users_id)
                ->where('coins_id', $parity->source_coin_id)
                ->first();
            $buySourceWallet->balance = \Litipk\BigNumbers\Decimal::fromString($buySourceWallet->balance)->sub(\Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($lastCalc['sell'])->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['sell_commission']), null)->innerValue()), null)->innerValue();
            $buySourceWallet->save();
            //\
            // Alıcıya Ödeme Gelecek Cüzdan
            $buyWallet = UserCoin::with('coin')
                ->where('users_id', $buy->users_id)
                ->where('coins_id', $parity->coin_id)
                ->first();
            $buyWallet->balance = \Litipk\BigNumbers\Decimal::fromString($buyWallet->balance)->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['buy']), null)->innerValue();
            $buyWallet->save();
            //\

            // Satıcının Ödeme Göndereceği Cüzdan
            $sellSourceWallet = UserCoin::with('coin')
                ->where('users_id', $sell->users_id)
                ->where('coins_id', $parity->coin_id)
                ->first();
            $sellSourceWallet->balance = \Litipk\BigNumbers\Decimal::fromString($sellSourceWallet->balance)->sub(\Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($lastCalc['buy'])->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['buy_commission']), null)->innerValue()), null)->innerValue();
            $sellSourceWallet->save();
            //\

            // Satıcıya Ödeme Gelecek Cüzdan
            $sellWallet = UserCoin::with('coin')
                ->where('users_id', $sell->users_id)
                ->where('coins_id', $parity->source_coin_id)
                ->first();
            $sellWallet->balance = \Litipk\BigNumbers\Decimal::fromString($sellWallet->balance)->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['sell']), null)->innerValue();
            $sellWallet->save();
            //\

            $buy->update($buyUpdate);
            $sell->update($sellUpdate);
            if ($buyUpdate['amount'] == 0) {
                $buy->delete();
            }
            if ($sellUpdate['amount'] == 0) {
                $sell->delete();
            }

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
                'amount' => \Litipk\BigNumbers\Decimal::fromString($lastCalc['buy'])->add(\Litipk\BigNumbers\Decimal::fromString($lastCalc['buy_commission']), null)->innerValue(),
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
    public function handle(): bool|string
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
