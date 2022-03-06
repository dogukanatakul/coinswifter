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
        if (empty($transaction = \App\Models\NodeTransaction::where('processed', 0)->first())) {
            return 'null';
        }
        $userCoin = \App\Models\UserCoin::with(['coin', 'user_wallet'])
            ->whereHas('coin', function ($q) use ($transaction) {
                $q->whereHas('network', function ($q) use ($transaction) {
                    $q->where('short_name', $transaction->network);
                });
                if (!empty($transaction->contract)) {
                    $q->where('contract', 'ilike', $transaction->contract);
                } else {
                    $q->whereNull('contract');
                }
            })
            ->whereHas('user_wallet', function ($q) use ($transaction) {
                $q->whereIn('wallet', [$transaction->from, $transaction->to]);
            })
            ->first();
        if (empty($userCoin)) {
            $transaction->processed = 3;
            $transaction->save();
            return "no_action";
        }

        $baseCoin = false;
        if (!empty($transaction->contract)) {
            $baseCoin = \App\Models\UserCoin::with(['coin'])
                ->whereHas('coin', function ($q) {
                    $q->whereNull('contract');
                })
                ->where('user_wallets_id', $userCoin->user_wallets_id)
                ->first();
        }


        if ($transaction->status == 0 && $transaction->progress === 'out') {
            if ($baseCoin) {
                $baseCoin->balance_pure = $baseCoin->balance_pure - $transaction->fee;
                $baseCoin->save();
            } else {
                $userCoin->balance_pure = $userCoin->balance_pure - $transaction->fee;
                $userCoin->save();
            }
            if (!empty($userWithdrawalWalletChild = UserWithdrawalWalletChild::where('txh', $transaction->txh)->first())) {
                $userWithdrawalWalletChild->status = 3;
                $userWithdrawalWalletChild->multiply = 1;
                $userWithdrawalWalletChild->save();
            }
        } else if ($transaction->status == 1 && $transaction->progress === 'out') {
            if ($baseCoin) {
                $baseCoin->balance_pure = $baseCoin->balance_pure - $transaction->fee;
                $baseCoin->save();
            } else {
                $userCoin->balance_pure = $userCoin->balance_pure - $transaction->fee;
                $userCoin->save();
            }
            $userCoin->balance_pure = $userCoin->balance_pure - $transaction->value;
            $userCoin->save();
            if (!empty($userWithdrawalWalletChild = UserWithdrawalWalletChild::where('txh', $transaction->txh)->first())) {
                $userWithdrawalWalletChild->status = 1;
                $userWithdrawalWalletChild->save();
                $userWithdrawalWalletChilds = UserWithdrawalWalletChild::where('user_withdrawal_wallets_id', $userWithdrawalWalletChild->user_withdrawal_wallets_id)
                    ->whereIn('status', [0, 2, 3])
                    ->get();
                if ($userWithdrawalWalletChilds->count() == 0) {
                    if (!empty($userWithdrawalWallet = UserWithdrawalWallet::where('id', $userWithdrawalWalletChild->user_withdrawal_wallets_id)->first())) {
                        $userCoin->balance = $userCoin->balance - $userWithdrawalWallet->amount;
                        $userCoin->save();
                        $userWithdrawalWallet->status = 1;
                        $userWithdrawalWallet->save();
                    }
                }
            }
        } else if ($transaction->status == 1 && $transaction->progress === 'in') {
            $userCoin->balance_pure = $userCoin->balance_pure + $transaction->value;
            $userCoin->balance = $userCoin->balance + $transaction->value;
            $userCoin->save();
        }
        if (!empty($userCoin)) {
            if ($transaction->progress === 'in') {
                $userCoin->balance = $userCoin->balance + $transaction->value;
                $userCoin->balance_pure = $userCoin->balance_pure + $transaction->value;
            }
            $userCoin->save();
        }
        $transaction->processed = 1;
        $transaction->save();
        return "success";
    }
}
