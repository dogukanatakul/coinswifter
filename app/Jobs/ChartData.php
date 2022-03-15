<?php

namespace App\Jobs;

use App\Models\OrderTransaction;
use App\Models\Parity;
use App\Models\ParityChart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
    public function handle(): bool
    {
        $parts = [
            '1min', // 1 dakikalık
            '5min', // 5 dakikalık
            '15min', // 15 dakikalık
            '30min', // 30 dakikalık
            '1hours', // 1 saatlik
            '4hours', // 4 saatlik
            '1day', // 1 günlük
            '1week', // 1 haftalık
            '1month', // 1 aylık
        ];
        $parities = Cache::remember('parity', now()->addDays(1), function () {
            return Parity::all();
        });
        $findChart = ParityChart::whereIn('type', $parts)
            ->orderBy('parities_id', 'ASC')
            ->orderBy('id', 'DESC')
            ->get()
            ->groupBy(['uuid', 'parities_id']);
        foreach ($parities as $parity) {
            if (empty($findChart->first()) || ($parity->id === $parities->first()->id && $findChart->first()->count() === $parities->count() && $findChart->first()->last()->count() === count($parts))) {
                $filterParts = $parts;
                $uuid = \Ramsey\Uuid\Uuid::uuid4();
            } else {
                if (isset($findChart->first()[$parity->id])) {
                    $filterParts = array_diff($parts, $findChart->first()[$parity->id]->pluck('type')->toArray());
                } else {
                    $filterParts = $parts;
                }
                $uuid = $findChart->first()->first()->first()->uuid;
            }
            foreach ($filterParts as $part) {
                $a = $this->intervalCalc($part);
                $b = $this->transactionCalc($a['periods'], $a['subDate'], $parity->id);
                ParityChart::create([
                    'parities_id' => $parity->id,
                    'type' => $part,
                    'data' => $b,
                    'uuid' => $uuid
                ]);
                return true;
            }
        }
    }

    public function transactionCalc($periods, $subDate, $parity): array
    {
        $list = [];
        $lastDate = false;
        foreach ($periods as $key => $value) {
            if ($subDate !== $value->format('Y-m-d H:i:s')) {
                $lastDate = $value->format('Y-m-d H:i:s');
                $swap = OrderTransaction::where('created_at', '>=', $subDate)
                    ->where('created_at', '<=', $value->format('Y-m-d H:i:s'))
                    ->where('parities_id', $parity)
                    ->get();
                $subDate = $value->format('Y-m-d H:i:s');
                $list[] = [
                    'x' => $value->format('D M d Y H:i:s O'),
                    'y' => [
                        $swap->first()->price ?? 0,
                        $swap->max('price') ?? 0,
                        $swap->min('price') ?? 0,
                        $swap->last()->price ?? 0,
                    ]
                ];
            }
        }
        if ($lastDate) {
            $swap = OrderTransaction::where('created_at', '>=', $lastDate)
                ->where('parities_id', $parity)
                ->get();
            $list[] = [
                'x' => now()->tz('Europe/Istanbul')->format('D M d Y H:i:s') . " +0000",
                'y' => [
                    $swap->first()->price ?? 0,
                    $swap->max('price') ?? 0,
                    $swap->min('price') ?? 0,
                    $swap->last()->price ?? 0,
                ]
            ];
        }
        return $list;
    }


    /**
     * @throws \Exception
     */
    public function intervalCalc($part): array|bool
    {
        if ($part === "1min") {
            $subDate = now()->tz('Europe/Istanbul')->subHours(1)->format('Y-m-d H:i:s');
            $interval = "PT1M";
        } else if ($part === "5min") {
            $subDate = now()->tz('Europe/Istanbul')->subMinutes(300)->format('Y-m-d H:i:s');
            $interval = "PT5M";
        } else if ($part === "15min") {
            $subDate = now()->tz('Europe/Istanbul')->subMinutes(900)->format('Y-m-d H:i:s');
            $interval = "PT15M";
        } else if ($part === "30min") {
            $subDate = now()->tz('Europe/Istanbul')->subMinutes(1800)->format('Y-m-d H:i:s');
            $interval = "PT15M";
        } else if ($part === "1hours") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(60)->format('Y-m-d H:i:s');
            $interval = "PT1H";
        } else if ($part === "4hours") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(240)->format('Y-m-d H:i:s');
            $interval = "PT4H";
        } else if ($part === "1day") {
            $subDate = now()->tz('Europe/Istanbul')->subDay(60)->format('Y-m-d H:i:s');
            $interval = "P1D";
        } else if ($part === "1week") {
            $subDate = now()->tz('Europe/Istanbul')->subWeek(60)->format('Y-m-d H:i:s');
            $interval = "P7D";
        } else if ($part === "1month") {
            $subDate = now()->tz('Europe/Istanbul')->subMonth(60)->format('Y-m-d H:i:s');
            $interval = "P1M";
        }
        if (isset($subDate) and isset($interval)) {
            // PT15M - PT60M - P4H - P1D - 1M
            return [
                'periods' => new \DatePeriod(
                    new \DateTime($subDate),
                    new \DateInterval($interval),
                    new \DateTime(now()->tz('Europe/Istanbul')->format('Y-m-d H:i:s'))
                ),
                'subDate' => $subDate
            ];
        } else {
            return false;
        }
    }
}
