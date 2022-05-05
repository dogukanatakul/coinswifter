<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class MarketMaker implements ShouldQueue
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
     */
    public function handle()
    {
        try {
            $parities_id = 3;
            $users_id = 1;
            $price = 10;
            $amount = 100;
            $amount_pure = 100;
            $total = $price * $amount;
            $type = "limit";
            $process = "buy";
            Order::create([
                'uuid' => Uuid::uuid4(),
                'parities_id' => $parities_id,
                'users_id' => $users_id,
                'price' => $price,
                'amount' => $amount,
                'amount_pure' => $amount_pure,
                'total' => $total,
                'type' => $type,
                'process' => $process,
                'microtime' => str_replace(".", "", microtime(true))
            ]);
        }
        catch (\Exception $e) {
            report($e);
        }
    }
}
