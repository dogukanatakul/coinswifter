<?php

namespace App\Jobs;

use App\Models\UserWithdrawalWallet;
use App\Models\UserWithdrawalWalletChild;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferTRON implements ShouldQueue
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
        //
    }

    /**
     * Execute the job.
     *
     * @return void
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
                $q->where('short_name', 'TRX');
            })
            ->whereHas('user_withdrawal_wallet', function ($q) {
                $q->whereNull('to_user_id')
                    ->where('created_at', '<', now()->tz('Europe/Istanbul')->subMinutes(1)->toDateTimeLocalString())
                    ->where('status', 0);
            })
            ->whereIn('status', [0, 3])
            ->whereNull('txh')
            ->first();
        if (empty($userWithdrawalWalletChild)) {
            return false;
        }
        if (($userWithdrawalWalletChild->status == 3 && $userWithdrawalWalletChild->error_answer === 'wrong_address') || $userWithdrawalWalletChild->multiply > 2) {
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
            'value' => priceFormat($amount),
        ];
        $transConf['multiply'] = $userWithdrawalWalletChild->multiply + 1;
        // eğer gas miktarı 3 katını geçmiş ise yeniden 1 katından başla.
        if ($userWithdrawalWalletChild->user_coin->coin->token_type === 'trc10') {
            $transConf['asset_name'] = $userWithdrawalWalletChild->user_coin->coin->contract;
            $transConf['token_type'] = "trc10";
        } elseif ($userWithdrawalWalletChild->user_coin->coin->token_type === 'trc20') {
            $transConf['fee_limit'] = 10000000;
            $transConf['contract_address'] = $userWithdrawalWalletChild->user_coin->coin->contract;
            $transConf['token_type'] = "trc20";
        }
        $trans = trxActions("set_transaction", $transConf);
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
