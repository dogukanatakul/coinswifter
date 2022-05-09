<?php

namespace App\Jobs;

use App\Models\Parity;
use App\Models\ParityPrice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CurrentPrices implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $timeout = 300;
    public int $uniqueFor = 10;
    public int $tries = 1;


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
     * @return bool
     */
    public function handle(): bool
    {
        $parities = Parity::withTrashed()
            ->with(['orders' => function ($q) {
                $q->where('price', '!=', 0)->orderBy('price', 'ASC');
            }, 'trades' => function ($q) {
                $q->where('created_at', '>=', now()->tz('Europe/Istanbul')->subDays(1))
                    ->orderBy('created_at', 'DESC');
            }, 'trade' => function ($q){
                $q->orderBy('id','DESC');
            }])
            ->get();
        DB::beginTransaction();
        foreach ($parities as $parity) {
            $params = [
                'price' => $parity->trade->price ?? 0.0,
                'volume_last_24_hours_price' => $parity->trades->sum('amount') ?? 0.0,
                'lowest' => $parity->trades->min('price') ?? 0.0,
                'highest' => $parity->trades->max('price') ?? 0.0,
            ];
            if ($parity->trades->count() > 1) {
                $params['percent_last_24_hours'] = ((1 - ($parity->trades->first()->price ?? 0 / $parity->trades->last()->price ?? 0)) * 100);
            }
            $parityExchanges = parityExchanges($params);
            //TODO: Güncel fiyat çekilirken php artisan upgrade:project ile tetiklendiğinde bir önceki fiyat eğer değişmediyse aynı kayıt insert ediliyor. Bunun kontrolünün sağlanması ve gerekirse aynı olduğu zaman eklenmemesi gerek.

            // ParityPrice::where('source', 'local')->where('parities_id', $parity->id)->forceDelete();
            foreach ($parityExchanges as $key => $value) {
                ParityPrice::create([
                    'parities_id' => $parity->id,
                    'value' => $value,
                    'source' => 'local',
                    'type' => $key,
                ]);
            }
        }
        DB::commit();
        return true;
    }
}
