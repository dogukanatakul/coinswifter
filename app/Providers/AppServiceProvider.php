<?php

namespace App\Providers;


use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Request::setTrustedProxies(['REMOTE_ADDR'], Request::HEADER_X_FORWARDED_FOR);

        Queue::after(function (JobProcessed $event) {
            $this->retry($event);
        });

        Queue::failing(function (JobFailed $event) {
            $this->retry($event, true);
        });
    }

    public function retry($event, $fail = false)
    {
        $jobName = $event->job->payload()['data']['commandName'];
        $param = (array)unserialize($event->job->payload()['data']['command']);
        $param = json_decode(str_replace("\u0000", "", json_encode($param)), true);
        if ($jobName === 'App\Jobs\CurrentPrices') {
            \App\Jobs\CurrentPrices::dispatch()->delay(now()->addMinutes(($fail ? 5 * 2 : 5)));
        } else if ($jobName === 'App\Jobs\TransferDexchain') {
            \App\Jobs\TransferDexchain::dispatch()->delay(now()->addMinutes(($fail ? 5 * 2 : 5)));
        } else if ($jobName === 'App\Jobs\TransferBSC') {
            \App\Jobs\TransferBSC::dispatch()->delay(now()->addMinutes(($fail ? 5 * 2 : 5)));
        } else if ($jobName === 'App\Jobs\TransferETH') {
            \App\Jobs\TransferETH::dispatch()->delay(now()->addMinutes(($fail ? 5 * 2 : 5)));
        } else if ($jobName === 'App\Jobs\Exchange') {
            \App\Jobs\Exchange::dispatch()->delay(now()->addMinutes(($fail ? 10 : 0)));
        } else if ($jobName === 'App\Jobs\CheckBanks') {
            \App\Jobs\CheckBanks::dispatch()->delay(now()->addMinutes(($fail ? 3 * 2 : 3)));
        } else if ($jobName === 'App\Jobs\ParityPrice') {
            \App\Jobs\ParityPrice::dispatch()->delay(now()->addMinutes(($fail ? 10 * 2 : 10)));
        } else if ($jobName === 'App\Jobs\TestJob') {
            \App\Jobs\TestJob::dispatch()->delay(now()->addMinutes(($fail ? 10 : 0)));
        }
    }
}
