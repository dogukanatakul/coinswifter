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
            \App\Jobs\CurrentPrices::dispatch()->onQueue('pricecalc')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 5 * 2 : 5)));
        } else if ($jobName === 'App\Jobs\TransferBSC') {
            \App\Jobs\TransferBSC::dispatch()->onQueue('transfer')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 1 : 0.7)));
        } else if ($jobName === 'App\Jobs\TransferETH') {
            \App\Jobs\TransferETH::dispatch()->onQueue('transfer')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 1 : 0.7)));
        } else if ($jobName === 'App\Jobs\TransferDB') {
            \App\Jobs\TransferDB::dispatch()->onQueue('transfer')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 0.5 * 2 : 0.5)));
        } else if ($jobName === 'App\Jobs\TransferTRON') {
            \App\Jobs\TransferTRON::dispatch()->onQueue('transfer')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 0.5 * 2 : 0.5)));
        } else if ($jobName === 'App\Jobs\NodeTransaction') {
            \App\Jobs\NodeTransaction::dispatch()->onQueue('checkamount')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 1 : 0.7)));
        } else if ($jobName === 'App\Jobs\Exchange') {
            \App\Jobs\Exchange::dispatch()->onQueue('exchange')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 10 : 0)));
        } else if ($jobName === 'App\Jobs\CheckBanks') {
            \App\Jobs\CheckBanks::dispatch()->onQueue('checkamount')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 3 * 2 : 3)));
        } else if ($jobName === 'App\Jobs\ParityPrice') {
            \App\Jobs\ParityPrice::dispatch()->onQueue('pricecalc')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 10 * 2 : 10)));
        } else if ($jobName === 'App\Jobs\ChartData') {
            \App\Jobs\ChartData::dispatch()->onQueue('chart')->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 0.5 * 2 : 0.5)));
        } else if ($jobName === 'App\Jobs\TestJob') {
            \App\Jobs\TestJob::dispatch()->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 10 : 0)));
        } else if ($jobName === 'App\Jobs\UserDeposit') {
            \App\Jobs\UserDeposit::dispatch()->delay(now()->tz('Europe/Istanbul')->addMinutes(($fail ? 5 : 1)));
        }
    }
}
