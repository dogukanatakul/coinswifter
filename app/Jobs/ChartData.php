<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChartData implements ShouldQueue
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
     * @throws \Exception
     */
    public function handle()
    {
        return "";
        $parts = [
            '15m', // 15 saniyelik
            '1h', // 1 saatlik
            '4h', // 4 saatlik
            '1d', // 1 günlük
            '1w', // 1 haftalık
            '1m', // 1 aylık
        ];
        foreach ($parts as $part) {
            dd($this->intervalCalc($part));
        }

    }


    /**
     * @throws \Exception
     */
    public function intervalCalc($part): \DatePeriod|bool
    {
        if ($part === "15m") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(1)->format('Y-m-d H:i:s');
            $interval = "PT15S";
        } else if ($part === "1h") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(20)->format('Y-m-d H:i:s');
            $interval = "PT60M";
        } else if ($part === "4h") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(80)->format('Y-m-d H:i:s');
            $interval = "PT240M";
        } else if ($part === "1d") {
            $subDate = now()->tz('Europe/Istanbul')->subDay(20)->format('Y-m-d H:i:s');
            $interval = "P1D";
        } else if ($part === "1w") {
            $subDate = now()->tz('Europe/Istanbul')->subWeek(20)->format('Y-m-d H:i:s');
            $interval = "P7D";
        } else if ($part === "1m") {
            $subDate = now()->tz('Europe/Istanbul')->subMonth(20)->format('Y-m-d H:i:s');
            $interval = "P1M";
        }
        if (isset($subDate) and isset($interval)) {
            // PT15M - PT60M - P4H - P1D - 1M
            return new \DatePeriod(
                new \DateTime($subDate),
                new \DateInterval($interval),
                new \DateTime(now()->tz('Europe/Istanbul')->format('Y-m-d H:i:s'))
            );
        } else {
            return false;
        }
    }
}
