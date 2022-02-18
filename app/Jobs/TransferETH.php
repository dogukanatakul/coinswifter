<?php

namespace App\Jobs;

use App\Jobs\Backups\BSCWalletControl;
use App\Models\Commission;
use App\Models\NodeLog;
use App\Models\ParityPrice;
use App\Models\Transferler;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class TransferETH implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public array $logs = [];
    public array $nodeLogs = [];
    public ?\Ramsey\Uuid\UuidInterface $uuid = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->logs = [];
        $this->nodeLogs = [];
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
    public function handle(): bool
    {
        $transfer = Transferler::with(['wallet' => function ($q) {
            $q->with(['coin' => function ($q) {
                $q->where('network', 'eth');
            }]);
        }, 'user'])
            ->whereNull('red_tarihi')
            ->whereNull('onay_tarihi')
            ->whereHas('wallet.coin')
            ->whereHas('user', function ($q) {
                $q->whereNull('islem_kilidi');
            })
            ->orderByRaw("RAND()")
            ->first();

        if (empty($transfer) || empty($transfer->wallet->coin)) {
            // transfer işlemi yok
            return false;
        }

        $coinPrice = ParityPrice::with(['parity' => function ($q) {
            $q->with(['coin', 'source']);
        }])->whereHas('parity.source', function ($q) {
            $q->where('sembol', 'TRY');
        })->whereHas('parity.coin', function ($q) use ($transfer) {
            $q->where('id', $transfer->wallet->coin->id);
        })->where('tipi', 'price')
            ->first();
        if (empty($coinPrice) || $coinPrice->sayisal <= 0) {
            $coinPrice = 0;
//            $this->failAction($transfer->kullanici_id, $transfer);
//            throw new \Exception("Transfer hatası!");
        } else {
            $coinPrice = $coinPrice->sayisal;
        }


        // Hesaplamalar
        if ($transfer->wallet->coin->transfer_komisyon_tipi === 'percent') {
            $commission = (($transfer->miktar * $transfer->wallet->coin->transfer_komisyon) / 100);
        } else {
            $commission = $transfer->miktar * $transfer->wallet->coin->transfer_komisyon;
        }
        $amount = $transfer->miktar - $commission;
        //\


        // Eğer BNB transfer etmek istiyorsa fee ücretini önceden kessin
        if (empty($transfer->wallet->coin->contract)) {
            $amountFeeConf = [
                'from_address' => $transfer->wallet->cuzdan_kodu,
                'to_address' => $transfer->alan_cuzdan,
                'value' => $amount,
            ];
            $amountFee = bscActions("fee_calculator", $amountFeeConf);
            $this->nodeLogs[] = [
                'operation' => 'transfer',
                'uuid' => $this->uuid,
                'request' => $amountFeeConf,
                'answer' => $amountFee->content
            ];
            if ($amountFee->status) {
                $amount = $amount - floatval($amountFee->content->bnb);
            } else {
                return $this->failAction($transfer->kullanici_id, $transfer);
            }

            $commissionFeeConf = [
                'from_address' => $transfer->wallet->cuzdan_kodu,
                'to_address' => bossWallets('bsc')->address,
                'value' => $commission,
            ];
            $commissionFee = bscActions("fee_calculator", $commissionFeeConf);
            $this->nodeLogs[] = [
                'operation' => 'transfer',
                'uuid' => $this->uuid,
                'request' => $commissionFeeConf,
                'answer' => $commissionFee->content
            ];
            if ($commissionFee->status) {
                $commission = $commission - floatval($commissionFee->content->bnb);
            } else {
                return $this->failAction($transfer->kullanici_id, $transfer);
            }
        }
        //\

        $step1Conf = [
            'from_address' => $transfer->wallet->cuzdan_kodu,
            'from_address_private' => $transfer->wallet->cuzdan_sifre,
            'to_address' => $transfer->alan_cuzdan,
            'value' => $amount,
        ];
        if (!empty($transfer->wallet->coin->contract)) {
            $step1Conf['contract_address'] = $transfer->wallet->coin->contract;
            $step1Conf['token_type'] = "erc20";
        }
        $step1 = bscActions("set_transaction", $step1Conf);
        $this->nodeLogs[] = [
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step1Conf,
            'answer' => $step1->content
        ];
        //\+
        if (!$step1->status) {
            return $this->failAction($transfer->kullanici_id, $transfer);
        } else {
            $this->logs[] = json_encode($step1Conf);
            $this->logs[] = json_encode($step1->content);
        }
        $step1ControlConf = ['txh' => $step1->content->txh];
        $step1Control = bscActions("get_transaction", $step1ControlConf);
        while ($step1Control->content->status === 'fail') {
            sleep(1);
            $step1Control = bscActions("get_transaction", $step1ControlConf);
        }
        $this->nodeLogs[] = [
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step1ControlConf,
            'answer' => $step1Control->content
        ];
        if ($step1Control->content->transaction_receipt->status == '0') {
            return $this->failAction($transfer->kullanici_id, $transfer);
        }
        // Adım 4 - Komisyonun Patron Cüzdanına Gelmesi
        $step2Conf = [
            'from_address' => $transfer->wallet->cuzdan_kodu,
            'from_address_private' => $transfer->wallet->cuzdan_sifre,
            'to_address' => bossWallets('bsc')->address,
            'value' => $commission,
        ];
        if (!empty($transfer->wallet->coin->contract)) {
            $step2Conf['contract_address'] = $transfer->wallet->coin->contract;
            $step2Conf['token_type'] = "erc20";
        }
        $step2 = bscActions("set_transaction", $step2Conf);
        $this->nodeLogs[] = [
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step2Conf,
            'answer' => $step2->content
        ];
        //\+
        if (!$step2->status) {
            $this->logs[] = "BSC step2 sorun.";
            return $this->failAction($transfer->kullanici_id, $transfer);
        } else {
            $this->logs[] = json_encode($step2Conf);
            $this->logs[] = json_encode($step2->content);
        }
        $step2ControlConf = ['txh' => $step2->content->txh];
        $step2Control = bscActions("get_transaction", $step2ControlConf);
        while ($step2Control->content->status === 'fail') {
            sleep(1);
            $step2Control = bscActions("get_transaction", $step2ControlConf);
        }
        $this->nodeLogs[] = [
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step2ControlConf,
            'answer' => $step2Control->content
        ];
        if ($step2Control->content->transaction_receipt->status == '0') {
            return $this->failAction($transfer->kullanici_id, $transfer);
        }
        BSCWalletControl::dispatch($transfer->kullanici_id);
        Commission::create([
            'transferler_id' => $transfer->id,
            'kullanici_id' => $transfer->kullanici_id,
            'cuzdan_id' => $transfer->wallet->id,
            'miktar' => $commission,
            'fiyat' => $commission * $coinPrice

        ]);
        $transfer->onay_tarihi = now()->toDateTimeString();
        $transfer->save();
        foreach ($this->nodeLogs as $nodeLog) {
            NodeLog::create($nodeLog);
        }
        return true;
    }


    public function failAction($user, $transfer): bool
    {
        User::where('id', $user)->update([
            'islem_kilidi' => now()->tz('Europe/Istanbul')->subDays(7)->toDateTimeString(),
            'islem_kilidi_sebebi' => "Yaşamış olduğumuz teknik aksaklıktan dolayı hesabınız inceleme altındadır."
        ]);
        $transfer->aciklama = "system_fail";
        $transfer->red_tarihi = now()->toDateTimeString();
        $transfer->save();
        foreach ($this->nodeLogs as $nodeLog) {
            NodeLog::create($nodeLog);
        }
        return false;
    }
}
