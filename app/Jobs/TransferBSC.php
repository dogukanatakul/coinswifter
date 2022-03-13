<?php

namespace App\Jobs;

use App\Models\UserWithdrawalWallet;
use App\Models\UserWithdrawalWalletChild;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class TransferBSC implements ShouldQueue
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
        $userWithdrawalWalletChild = UserWithdrawalWalletChild::with([
            'user_coin' => function ($q) {
                $q->with([
                    'user_wallet',
                    'coin' => function ($q) {
                        $q->with('network');
                    }]);
            },
            'user_withdrawal_wallet'
        ])
            ->whereHas('user_coin.coin.network', function ($q) {
                $q->where('short_name', 'BSC');
            })
            ->whereHas('user_withdrawal_wallet', function ($q) {
                $q->whereNull('to_user_id')
                    ->where('created_at', '<', now()->subMinutes(1)->toDateTimeLocalString())
                    ->where('status', 0);
            })
            ->whereIn('status', [0, 3])
            ->whereNull('txh')
            ->first();
        if (empty($userWithdrawalWalletChild)) {
            return false;
        }
        if ($userWithdrawalWalletChild->status == 3 && (preg_match("/ENS name: '.*' is invalid./", $userWithdrawalWalletChild->error_answer) == 1) || preg_match("/Unknown format .*, attempted to normalize to 0xa/", $userWithdrawalWalletChild->error_answer) == 1) {
            UserWithdrawalWallet::where('id', $userWithdrawalWalletChild->user_withdrawal_wallets_id)
                ->update([
                    'status' => 3
                ]);
            return false;
        }
        $amount = $userWithdrawalWalletChild->amount;
        //\
        $transConf = [
            'from_address' => $userWithdrawalWalletChild->user_coin->user_wallet->wallet,
            'from_address_private' => $userWithdrawalWalletChild->user_coin->user_wallet->password,
            'to_address' => $userWithdrawalWalletChild->user_withdrawal_wallet->to,
            'value' => $amount,
        ];
        $transConf['multiply'] = $userWithdrawalWalletChild->multiply > 3 ? 1 : $userWithdrawalWalletChild->multiply + 1;
        // eğer gas miktarı 3 katını geçmiş ise yeniden 1 katından başla.
        if (!empty($userWithdrawalWalletChild->user_coin->coin->contract)) {
            $transConf['contract_address'] = $userWithdrawalWalletChild->user_coin->coin->contract;
            $transConf['token_type'] = "bep20";
        }
        $trans = bscActions("set_transaction", $transConf);
        //\+
        if (!$trans->status) {
            $userWithdrawalWalletChild->status = 3;
            $userWithdrawalWalletChild->error_answer = $trans->content->message;
        } else {
            $userWithdrawalWalletChild->txh = $trans->content->txh;
            $userWithdrawalWalletChild->status = 2;
        }
        $userWithdrawalWalletChild->multiply = $transConf['multiply'];
        $userWithdrawalWalletChild->save();
        return true;
    }
}
