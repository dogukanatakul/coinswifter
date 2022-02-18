<?php

namespace App\Jobs;

use App\Models\UserWithdrawalWallet;
use App\Models\UserWithdrawalWalletChild;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NodeTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @return string
     */
    public function handle(): string
    {
        if (empty($transaction = \App\Models\NodeTransaction::where('status', 0)->first())) {
            return 'null';
        }
        if (empty($userWallet = \App\Models\UserWallet::whereIn('wallet', [$transaction->from, $transaction->to])->first())) {
            return 'null';
        }
        $userCoin = \App\Models\UserCoin::with(['coin' => function ($q) use ($transaction) {
            if (!empty($transaction->contract)) {
                $q->where('contract', 'ilike', $transaction->contract);
            } else {
                $q->where('contract', $transaction->contract);
            }
        }])
            ->where('user_wallets_id', $userWallet->id)
            ->first();
        if (!empty($userCoin)) {
            if ($transaction->progress === 'in') {
                $userCoin->balance = $userCoin->balance + $transaction->value;
                $userCoin->balance_pure = $userCoin->balance_pure + $transaction->value;
            } else {
                $userCoin->balance_pure = $userCoin->balance_pure - $transaction->value;
                $userWithdrawalWalletChild = UserWithdrawalWalletChild::with(['user_coin' => function ($q) use ($userWallet) {
                    $q->with(['user_wallet' => function ($q) use ($userWallet) {
                        $q->where('id', $userWallet->id);
                    }]);
                }, 'user_withdrawal_wallet' => function ($q) use ($transaction) {
                    $q->where('to', $transaction->to);
                }])
                    ->whereHas('user_coin.user_wallet')
                    ->whereHas('user_withdrawal_wallet')
                    ->where('amount', $transaction->value)
                    ->where('status', 0)
                    ->whereNotNull('txh')
                    ->first();
                if (!empty($userWithdrawalWalletChild)) {
                    $userWithdrawalWalletChild->status = 1;
                    $userWithdrawalWalletChild->save();
                    $userWithdrawalWallet = UserWithdrawalWallet::with(['user_withdrawal_wallet_child' => function ($q) {
                        $q->where('status', 0);
                    }])
                        ->where('id', $userWithdrawalWalletChild->user_withdrawal_wallets_id)
                        ->first();
                    if ($userWithdrawalWallet->user_withdrawal_wallet_child->count() == 0) {
                        $userWithdrawalWallet->status = 1;
                        $userWithdrawalWallet->save();
                    }
                }
            }
            $userCoin->save();
        }
        $transaction->status = 1;
        $transaction->save();
        return "success";
    }
}
