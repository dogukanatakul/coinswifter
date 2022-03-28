<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserBank;
use App\Models\UserCoin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserDeposit implements ShouldQueue
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
     * @return void
     */
    public function handle()
    {
        $deposits = \App\Models\UserDeposit::where('status', 0)->get();
        foreach ($deposits as $deposit) {
            $longName = explode(" ", $deposit->sender_name);
            $surName = $longName[count($longName) - 1];
            unset($longName[count($longName) - 1]);
            $firstName = implode(" ", $longName);
            if (!empty($userCheck = User::where('name', 'LIKE', $firstName)->where('surname', 'LIKE', $surName)->where('tck_no', $deposit->tkc_no)->first())) {
                $userCoin = UserCoin::with(['coin'])
                    ->where('users_id', $userCheck->id)
                    ->whereHas('coin', function ($q) use ($deposit) {
                        $q->where('symbol', $deposit->currency);
                    })
                    ->first();
                $userBank = UserBank::where('users_id', $userCheck->id)
                    ->where('iban', $deposit->iban)
                    ->first();
                if (!empty($userCoin) && !empty($userBank)) {
                    $userCoin->balance = \Litipk\BigNumbers\Decimal::fromString($userCoin->balance)->add(\Litipk\BigNumbers\Decimal::fromString($deposit->amount), null)->innerValue();
                    $userCoin->save();
                    $deposit->users_id = $userCheck->id;
                    $deposit->user_banks_id = $userBank->id;
                    $deposit->status = 1;
                } else {
                    $deposit->status = 3;
                }
                $deposit->save();
            }
        }
    }
}
