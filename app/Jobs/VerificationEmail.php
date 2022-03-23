<?php

namespace App\Jobs;

use App\Models\UserVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class VerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $timeout = 300;

    protected $user;
    protected $settings;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $settings = false)
    {
        $this->user = $user;
        $this->settings = $settings;
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

            $contact = collect($contacts)->mapWithKeys(function ($item) {
                return [$item['type'] => $item];
            })->toArray()['email'];
            $setCode = new UserVerification();
            $setCode->users_id = $this->user['id'];
            $setCode->code = rand(111111, 999999);
            $setCode->type = "email";
            $setCode->save();
            if (!empty($setCode)) {
                $this->settings['code'] = $setCode->code;
                $this->settings['url'] = url("?code=" . $setCode->code);
                $this->settings['unsubscribe'] = url("?unsubscribe=" . $contact['value']);
                $this->settings['locked'] = !$this->settings['locked'] ?? url("?locked=" . $contact['value']);

                Mail::send('mails.verification', ["template" => $this->settings], function ($message) use ($contact) {
                    $message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
                    $message->to($contact['value']);
                    $message->subject(config('app.name') . " - " . $this->settings['title'] . " | " . $this->settings['code']);
                });
            }
            return true;
        } catch (\Exception $e) {
            $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            throw new \Exception($e);
        }
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }
}
