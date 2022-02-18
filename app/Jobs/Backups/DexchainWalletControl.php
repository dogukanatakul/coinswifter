<?php

namespace App\Jobs\Backups;

use App\Jobs\NotificationNewAmount;
use App\Models\Coin;
use App\Models\CuzdanTanim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use function dxcActions;

class DexchainWalletControl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $timeout = 300;
    public $tries = 1;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user = false)
    {
        $this->user = $user;
    }


    public static function keepMonitorOnSuccess(): bool
    {
        return false;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $coins = Coin::where('network', 'dxc')->get();
        $wallets = CuzdanTanim::with('coin', 'user.contacts')
            ->whereIn('coin_id', $coins->pluck('id')->toArray());
        if ($this->user) {
            $wallets = $wallets->where('kullanici_id', $this->user);
        }
        $wallets = $wallets->get();
        foreach ($wallets as $wallet) {
            if ($wallet->coin->contract === "") {
                if (($w = dxcActions("balance_wallet", ['address' => $wallet->cuzdan_kodu])) && $w->status) {
                    $this->notification($wallet->bakiye, $w->content->value, $wallet->coin, $wallet->user);
                    $wallet->bakiye = floatval($w->content->value);
                    $wallet->save();
                } else {
                    throw new \Exception("node hatası: " . $wallet->cuzdan_kodu);
                }
            } else {
                if (($w = dxcActions("balance_token", ['address' => $wallet->cuzdan_kodu, 'contract' => $wallet->coin->contract])) && $w->status) {
                    $this->notification($wallet->bakiye, $w->content->value, $wallet->coin, $wallet->user);
                    $wallet->bakiye = floatval($w->content->value);
                    $wallet->save();
                } else {
                    throw new \Exception("node hatası: " . $wallet->cuzdan_kodu);
                }
            }
        }
        return true;
    }

    public function notification(float $old, float $new, object $coin, object $user)
    {
        if ($old != $new) {
            $settings = [
                'title' => $old < $new ? 'Hesabınızda Bakiye Girişi Oldu!' : 'Hesabınızdan bakiye çıkışı oldu',
                'description' => $coin->isim . " cüzdanınızdaki bakiye değişikliği miktarı: " . ($new - $old),
            ];
            NotificationNewAmount::dispatch($user->toArray(), $settings);
        }
        return;
    }

}
