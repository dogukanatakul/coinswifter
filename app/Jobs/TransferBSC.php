<?php

namespace App\Jobs;

use App\Models\UserWithdrawalWalletChild;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class TransferBSC implements ShouldQueue, ShouldBeUnique
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
        $userWithdrawalWalletChild = UserWithdrawalWalletChild::with(['user_coin' => function ($q) {
            $q->with(['user_wallet', 'coin']);
        }, 'user_withdrawal_wallet'])
            ->whereIn('status', [0, 3])
            ->whereNull('txh')
            ->first();
        if (empty($userWithdrawalWalletChild)) {
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
        $transConf['multiply'] = intval($userWithdrawalWalletChild->multiply) + 1;
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
            $userWithdrawalWalletChild->status = 1;
        }
        $userWithdrawalWalletChild->multiply = $transConf['multiply'];
        $userWithdrawalWalletChild->save();

        dd($userWithdrawalWalletChild, $transConf, $trans);
        return true;
    }
}
