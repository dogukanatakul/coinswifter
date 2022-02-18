<?php

namespace App\Jobs;

use App\Models\CuzdanTanim;
use App\Models\UserBank;
use App\Models\User;
use App\Models\UserCoin;
use App\Models\UserDeposit;
use Dflydev\DotAccessData\Data;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckBanks implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $timeout = 300;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public static function keepMonitorOnSuccess(): bool
    {
        return false;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ls = Curl::to('http://185.126.177.148:6789/api/home/BorsaBankaIslemler')
            ->asJsonResponse()
            ->returnResponseObject()
            ->withTimeout(5)
            ->withConnectTimeout(5)
            ->get();
        if ($ls->status === 200) {
            foreach ($ls->content as $content) {
                if (empty(UserDeposit::where('unique_token', $content->DekontNo)->where('bank_name', $content->BankaAdi)->first())) {
                    $longName = explode(" ", $content->Gonderici);
                    $surName = $longName[count($longName) - 1];
                    unset($longName[count($longName) - 1]);
                    $firstName = implode(" ", $longName);
                    if (!empty($userCheck = User::where('name', 'LIKE', $firstName)->where('surname', 'LIKE', $surName)->where('tck_no', $content->BorcluTc)->first())) {
                        if (!empty($userBank = UserBank::where('users_id', $userCheck->id)->where('iban', $content->Iban)->first())) {
                            $coin = $content->Parite === "TRY" ? 1 : 2;
                            if (!empty($wallet = UserCoin::where('coins_id', $coin)->where('users_id', $userCheck->id)->first())) {
                                DB::beginTransaction();
                                try {
                                    UserDeposit::create([
                                        'coins_id' => $coin,
                                        'user_banks_id' => $userBank->id,
                                        'amount' => floatval($content->Tutar),
                                        'unique_token' => trim($content->DekontNo),
                                        'bank_name' => $content->BankaAdi,
                                    ]);
                                    $wallet->balance = $wallet->balance + floatval($content->Tutar);
                                    $wallet->save();
                                    DB::commit();
                                } catch (\Exception $e) {
                                    DB::rollBack();
                                    $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
                                    throw new \Exception($e);
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
}
