<?php

namespace App\Jobs;

use App\Jobs\Backups\DexchainWalletControl;
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
use romanzipp\QueueMonitor\Traits\IsMonitored;

class TransferDexchain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $timeout = 120;
    public $logs = [];
    public $uuid = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->logs = [];
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
    public function handle()
    {
        $transfer = Transferler::with(['wallet' => function ($q) {
            $q->with(['coin' => function ($q) {
                $q->where('network', 'dxc');
            }]);
        }, 'user'])->whereNull('red_tarihi')
            ->whereNull('onay_tarihi')
            ->whereHas('wallet.coin')
            ->whereHas('user', function ($q) {
                $q->whereNull('islem_kilidi');
            })
            ->orderByRaw("RAND()")
            ->first();
        if (empty($transfer) || empty($transfer->wallet->coin)) {
            // transfer işlemi yok
            return;
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
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
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

        $getFeeConf = [
            'amount' => $amount,
            'contract' => empty($transfer->wallet->coin->contract) ? 'mydexchain' : $transfer->wallet->coin->contract
        ];
        $getFee = dxcActions("get_fee", $getFeeConf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $getFeeConf,
            'answer' => $getFee->content
        ]);
        if (!$getFee->status) {
            $this->logs[] = "Dexchain getFee sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($getFee->content);
        }

        // Adım 1 - Alıcıya ve Patrona Gönderim İşlemi Yapacak Kişiye Dexchain Gönderme
        $step1Conf = [
            'sender' => bossWallets('dxc')->address,
            'password' => bossWallets('dxc')->password,
            'receiver' => $transfer->gonderen_cuzdan,
            'amount' => $getFee->fee,
            'fee' => 'OUT',
            'contract' => 'mydexchain',
            'description' => 'transfer',
        ];
        $step1 = dxcActions("send", $step1Conf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step1Conf,
            'answer' => $step1->content
        ]);
        //\ +
        if (!$step1->status) {
            $this->logs[] = "Dexchain step1 sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step1Conf);
            $this->logs[] = json_encode($step1->content);
        }

        $step1ControlConf = ['dexhash' => $step1->Dexhash];
        $step1Control = dxcActions("get_transaction_by_dex_hash", $step1ControlConf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step1ControlConf,
            'answer' => $step1Control->content
        ]);
        if (!$step1Control->status) {
            $this->logs[] = "Dexchain step1 control sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step1Control->content);
        }


        // Adım 2 - Alıcıya Gönderim
        $step2Conf = [
            'sender' => $transfer->wallet->cuzdan_kodu,
            'password' => $transfer->wallet->cuzdan_sifre,
            'receiver' => $transfer->alan_cuzdan,
            'amount' => $amount,
            'fee' => '0,003',
            'contract' => empty($transfer->wallet->coin->contract) ? 'mydexchain' : $transfer->wallet->coin->contract,
            'description' => 'transfer',
        ];
        $step2 = dxcActions("send", $step2Conf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step2Conf,
            'answer' => $step2->content
        ]);
        //\+
        if (!$step2->status) {
            $this->logs[] = "Dexchain step2 sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step2Conf);
            $this->logs[] = json_encode($step2->content);
        }


        $step2ControlConf = ['dexhash' => $step2->Dexhash];
        $step2Control = dxcActions("get_transaction_by_dex_hash", $step2ControlConf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step2ControlConf,
            'answer' => $step2Control->content
        ]);
        if (!$step2Control->status) {
            $this->logs[] = "Dexchain step2 control sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step2Control->content);
        }


        $getFeeConf = [
            'amount' => $commission,
            'contract' => empty($transfer->wallet->coin->contract) ? 'mydexchain' : $transfer->wallet->coin->contract
        ];
        $getFee = dxcActions("get_fee", $getFeeConf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $getFeeConf,
            'answer' => $getFee->content
        ]);
        if (!$getFee->status) {
            $this->logs[] = "Dexchain getFee sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($getFee->content);
        }


        // Adım 3 - Komisyon ödeyecek ALAN kişiye dexchain gönderme
        $step3Conf = [
            'sender' => bossWallets('dxc')->address,
            'password' => bossWallets('dxc')->password,
            'receiver' => $transfer->wallet->cuzdan_kodu,
            'amount' => $getFee->fee,
            'fee' => 'OUT',
            'contract' => 'mydexchain',
            'description' => 'transfer',
        ];
        $step3 = dxcActions("send", $step3Conf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step3Conf,
            'answer' => $step3->content
        ]);
        //\+
        if (!$step3->status) {
            $this->logs[] = "Dexchain step3 sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step3Conf);
            $this->logs[] = json_encode($step3->content);
        }

        $step3ControlConf = ['dexhash' => $step3->Dexhash];
        $step3Control = dxcActions("get_transaction_by_dex_hash", $step3ControlConf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step3ControlConf,
            'answer' => $step3Control->content
        ]);
        if (!$step3Control->status) {
            $this->logs[] = "Dexchain step3 control sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step3Control->content);
        }


        // Adım 4 - Komisyonun Patron Cüzdanına Gelmesi
        $step4Conf = [
            'sender' => $transfer->wallet->cuzdan_kodu,
            'password' => $transfer->wallet->cuzdan_sifre,
            'receiver' => bossWallets('dxc')->address,
            'amount' => $commission,
            'fee' => '0,003',
            'contract' => empty($transfer->wallet->coin->contract) ? 'mydexchain' : $transfer->wallet->coin->contract,
            'description' => 'transfer',
        ];
        $step4 = dxcActions("send", $step4Conf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step4Conf,
            'answer' => $step4->content
        ]);
        //\+
        if (!$step4->status) {
            $this->logs[] = "Dexchain step4 sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step4Conf);
            $this->logs[] = json_encode($step4->content);
        }


        $step4ControlConf = ['dexhash' => $step4->Dexhash];
        $step4Control = dxcActions("get_transaction_by_dex_hash", $step4ControlConf);
        NodeLog::create([
            'operation' => 'transfer',
            'uuid' => $this->uuid,
            'request' => $step4ControlConf,
            'answer' => $step4Control->content
        ]);
        if (!$step4Control->status) {
            $this->logs[] = "Dexchain step4 control sorun.";
            $this->failAction($transfer->kullanici_id, $transfer);
            throw new \Exception("Transfer hatası!");
        } else {
            $this->logs[] = json_encode($step4Control->content);
        }

        DexchainWalletControl::dispatch($transfer->kullanici_id);
        Commission::create([
            'transferler_id' => $transfer->id,
            'kullanici_id' => $transfer->kullanici_id,
            'cuzdan_id' => $transfer->wallet->id,
            'miktar' => $commission,
            'fiyat' => $commission * $coinPrice

        ]);
        $transfer->onay_tarihi = now()->toDateTimeString();
        $transfer->save();
        return true;
    }

    public function failAction($user, $transfer)
    {
        User::where('id', $user)->update([
            'islem_kilidi' => now()->tz('Europe/Istanbul')->subDays(7)->toDateTimeString(),
            'islem_kilidi_sebebi' => "Yaşamış olduğumuz teknik aksaklıktan dolayı hesabınız inceleme altındadır."
        ]);
        $transfer->aciklama = "system_fail";
        $transfer->red_tarihi = now()->toDateTimeString();
        $transfer->save();
        return true;
    }

}
