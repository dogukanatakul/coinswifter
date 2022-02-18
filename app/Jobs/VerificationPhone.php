<?php

namespace App\Jobs;

use App\Models\UserVerification;
use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class VerificationPhone implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $timeout = 300;
    public $uniqueFor = 10;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
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

        try {

            $contacts = $this->user['contacts'];
            if ($this->user['status'] === 31) {
                $contacts = collect($contacts)->filter(function ($data) {
                    return $data['status'] === 3;
                })->toArray();
            } else if ($this->user['status'] === 32) {
                $contacts = collect($contacts)->filter(function ($data) {
                    return $data['status'] === 4;
                })->toArray();
            }
            // https://www.netgsm.com.tr/dokuman/
            $contact = collect($contacts)->mapWithKeys(function ($item, $key) {
                return [$item['type'] => $item];
            })->toArray()['telephone'];

            $setCode = new UserVerification();
            $setCode->users_id = $this->user['id'];
            $setCode->code = rand(111111, 999999);
            $setCode->type = "telephone";
            $setCode->save();
            if (!empty($setCode)) {
                $conf = [];
                $conf['usercode'] = "2243340192";
                $conf['password'] = "L5-EU55E";
                if ($contact['nationality'] === 218) {
                    $conf['gsmno'] = $contact['value'];
                    $conf['message'] = env('APP_NAME') . " üyelik doğrulama kodunuz: " . $setCode->code;
                } else {
                    $code = Country::where('id', $contact['nationality'])->first()->phone_code;
                    $conf['gsmno'] = "00" . $code . $contact['value'];
                    $conf['message'] = "Your " . env('APP_NAME') . " verification code is : " . $setCode->code;
                }
                $conf['msgheader'] = "RAYSOFT";
                $conf = http_build_query($conf, '', '&');
                $response = Curl::to('https://api.netgsm.com.tr/sms/send/get/?' . $conf)
                    ->get();
                if (str_contains($response, " ")) {
                    if (explode(" ", $response)[0] == "00") {
                        printf("success\n\r");
                        $this->queueData(['status' => 'success', 'message' => $response]);
                        return;
                    } else {
                        $this->queueData(['status' => 'fail', 'message' => $response]);
                        throw new \Exception("fail_1");
                    }
                } else {
                    $this->queueData(['status' => 'fail', 'message' => $response]);
                    throw new \Exception("fail_2");
                }
            }
        } catch (\Exception $e) {
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }
}
