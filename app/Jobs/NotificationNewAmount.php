<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotificationNewAmount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contact = collect($this->user['contacts'])->mapWithKeys(function ($item) {
            return [$item['type'] => $item];
        })->toArray()['email'];

        Mail::send('mails.amount', ["template" => $this->settings], function ($message) use ($contact) {
            $message->from(env('MAIL_FROM_ADDRESS'), config('app.name'));
            $message->to($contact['deger']);
            $message->subject(config('app.name') . " - " . $this->settings['title']);
        });
        return;
    }
}
