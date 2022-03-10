<?php

namespace App\Jobs;

use App\Models\UserCoin;
use App\Models\UserWithdrawalWallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferDB implements ShouldQueue
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
        if (!empty($userWithdrawalWallet = UserWithdrawalWallet::whereNotNull('to_user_id')->where('created_at', '>', now()->subMinutes(1)->toDateTimeLocalString())->where('status', 0)->first())) {
            if (!empty($toUser = UserCoin::where('users_id', $userWithdrawalWallet->to_user_id)->where('coins_id', $userWithdrawalWallet->coins_id)->first())) {
                $senderUser = UserCoin::where('id', $userWithdrawalWallet->user_coins_id)->first();
                $senderUser->balance = $senderUser->balance - $userWithdrawalWallet->amount;
                $senderUser->save();
                $toUser->balance = $toUser->balance + $userWithdrawalWallet->send_amount;
                $toUser->save();
                $userWithdrawalWallet->status = 1;
                $userWithdrawalWallet->save();
                return "success";
            }
        }
        return "null";
    }
}
