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
use App\Models\Parity;
use App\Models\ParityPrice;

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
            // dd(PHP_INT_MAX);
            $bot_parities = \App\Models\MarketMaker::with('parities.source', 'parities.coin')->whereNull('deleted_at')->get();
            $counter = $bot_parities->count();
            for ($c = 0; $c < $counter; $c++) {
                // dd($bot_parities[$i]['parities_id']); Parite çiftlerinin fiyatlarını çekme

                $market = Parity::with(['source', 'coin', 'parity_price', 'commission'])
                    ->whereHas('commission')
                    ->orderBy('order', 'ASC')
                    ->get();

                $market = collect($market)->mapWithKeys(function ($item, $key) {
                    $newItem = $item->toArray();
                    if (count($newItem['parity_price']) === 0) {
                        $newItem['parity_price'] = collect(parityExchanges())->mapWithKeys(function ($item, $key) {
                            return [$key => ["value" => $item, "status" => '']];
                        })->toArray();
                    } else {
                        $newItem['parity_price'] = collect($item->parity_price->groupBy('type'))->mapWithKeys(function ($item, $key) {
                            $status = $item->first()->value > $item->last()->value ? "down" : 'up';
                            $price = priceFormat($item->last()->value);
                            return [$item->last()->type => ["value" => $price, "status" => $status]];
                        });
                    }
                    $newItem['commission'] = priceFormat($item['commission']['commission']);
                    return [$item['source']['symbol'] . "-" . $item['coin']['symbol'] => $newItem];
                })->toArray();
                //MarketMaker alış - satış emirlerini girme (Parametreler baz alınacak)
                if (!empty($market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']])) {

                    $is_btc_base = $bot_parities[$c]['btc_primary']; //BTC bazında emir oluşturulsun mu ?
                    $current_price = $market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']]['parity_price']['price']['value'];
                    $users_id = $bot_parities[$c]['users_id'];
                    $btc_parities_id = $bot_parities[$c]['btc_parities_id'];
                    $parities_id = $bot_parities[$c]['parities_id'];
                    $buy_spread = $bot_parities[$c]['buy_spread']; //%'lik aralık olacak
                    $sell_spread = $bot_parities[$c]['sell_spread']; //%'lik aralık olacak
                    $buy_order_count = $bot_parities[$c]['buy_order_count']; //Buy emir sayısı
                    $sell_order_count = $bot_parities[$c]['sell_order_count']; //Sell emir sayısı
                    $btc_buy_spread = $bot_parities[$c]['btc_buy_spread'];
                    $btc_sell_spread = $bot_parities[$c]['btc_sell_spread'];
                    $btc_buy_order_count = $bot_parities[$c]['btc_buy_order_count'];
                    $btc_sell_order_count = $bot_parities[$c]['btc_sell_order_count'];
                    $up_current_price = $current_price + ($current_price * $sell_spread) / 100;
                    $down_current_price = $current_price - ($current_price * $buy_spread) / 100;
                    $min_token = $bot_parities[$c]['min_token'];
                    $max_token = $bot_parities[$c]['max_token'];
                    $scale_count = $bot_parities[$c]['scale_count'];
                    $price_scale_count = $bot_parities[$c]['price_scale_count'];

                    if ($current_price !== 0) {
                        if ($is_btc_base === true) {

                            if (($parities = ParityPrice::where('parities_id', $btc_parities_id)) && $parities->get()->count() > 0) {
                                $parity_price = $parities->where('parities_id', $btc_parities_id)->where('type', 'price')->where('source', 'local')->orderBy('id', 'DESC')->limit(2)->get();
                                $old_price = $parity_price->last()['value'];
                                $new_price = $parity_price->first()['value'];

                                // dd($old_price,$new_price);
                                if ($old_price == 0 || $new_price == 0) {
                                    throw new \Exception("Son fiyatlar 0 dan farklı olmalıdır.");
                                }

                                $parity_diff = (100 / $old_price) * $new_price;
                                if (($old_price - $new_price) > 0) {
                                    $percent = 100 - $parity_diff;
                                    // dd($percent . " lan sat gidiyor  a q qq");

                                    //Emir Girişleri Komutu dd($percent);
                                    try {
                                        $randomDecimal = $current_price;
                                        $buy_decimal = $randomDecimal - (($randomDecimal * $percent) / 100);
                                        $down_current_btc_price = $buy_decimal - (($buy_decimal * $btc_buy_spread) / 100);
                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                        if (!empty($is_parity_orders_buy->toArray())) {

                                            $parity_orders_buy_count = $is_parity_orders_buy->count();
                                            if ($parity_orders_buy_count > $btc_buy_order_count) {
                                                for ($i = 0; $i < abs($parity_orders_buy_count - $btc_buy_order_count); $i++) {
                                                    $is_parity_orders_buy = collect($is_parity_orders_buy)->map(function ($item) {
                                                        return $item;
                                                    })->sortBy('price')->first();
                                                    Order::where('id', $is_parity_orders_buy->id)->delete();
                                                    $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                                }
                                            } else {
                                                //Eski fiyatları güncelleme komutu
                                                for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));
                                                    }
                                                    // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));


                                                    $is_parity_orders_buy[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }

                                                //Ekleme Komutu
                                                for ($i = 0; $i < abs($btc_buy_order_count - $parity_orders_buy_count); $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));

                                                    // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $amount = $orderAmount;
                                                    $price = $randomDecimal;
                                                    $total = $price * $amount;
                                                    $type = "limit";
                                                    $process = "buy";
                                                    Order::create([
                                                        'uuid' => Uuid::uuid4(),
                                                        'parities_id' => $parities_id,
                                                        'users_id' => $users_id,
                                                        'price' => $price,
                                                        'amount' => $amount,
                                                        'total' => $total,
                                                        'type' => $type,
                                                        'process' => $process,
                                                        'primary' => true,
                                                        'microtime' => str_replace(".", "", microtime(true))
                                                    ]);
                                                }
                                            }
                                            // dd($parity_orders_buy_count); //Null Check yapılacak

                                        } else {
                                            // dd($randomDecimal, $up_current_btc_price);
                                            for ($i = 0; $i < $btc_buy_order_count; $i++) { //Satış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }

                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));

                                                // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "buy";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }

                                        $current_price = $market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']]['parity_price']['price']['value'];
                                        // $orderAmount = rand($min_token, $max_token);
                                        $randomDecimal = $current_price;
                                        $sell_decimal = $randomDecimal - (($randomDecimal * $percent) / 100);

                                        $up_current_btc_price = $sell_decimal + (($sell_decimal * $btc_sell_spread) / 100);

                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                        if (!empty($is_parity_orders_sell->toArray())) {

                                            $parity_orders_sell_count = $is_parity_orders_sell->count();
                                            if ($parity_orders_sell_count > $btc_sell_order_count) {
                                                for ($i = 0; $i < abs($parity_orders_sell_count - $btc_sell_order_count); $i++) {
                                                    $is_parity_orders_sell = collect($is_parity_orders_sell)->map(function ($item) {
                                                        return $item;
                                                    })->sortBy('price')->first();
                                                    Order::where('id', $is_parity_orders_sell->id)->delete();
                                                    $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                                }
                                            } else {
                                                for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    // $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.'.$price_scale_count.'f',$up_current_btc_price * $div), sprintf('%.'.$price_scale_count.'f',$sell_decimal * $div)) / $div));
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $is_parity_orders_sell[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }

                                                for ($i = 0; $i < abs($btc_sell_order_count - $parity_orders_sell_count); $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    // $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.'.$price_scale_count.'f',$up_current_btc_price * $div), sprintf('%.'.$price_scale_count.'f',$sell_decimal * $div)) / $div));
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $amount = $orderAmount;
                                                    $price = $randomDecimal;
                                                    $total = $price * $amount;
                                                    $type = "limit";
                                                    $process = "sell";
                                                    Order::create([
                                                        'uuid' => Uuid::uuid4(),
                                                        'parities_id' => $parities_id,
                                                        'users_id' => $users_id,
                                                        'price' => $price,
                                                        'amount' => $amount,
                                                        'total' => $total,
                                                        'type' => $type,
                                                        'process' => $process,
                                                        'primary' => true,
                                                        'microtime' => str_replace(".", "", microtime(true))
                                                    ]);
                                                }
                                            }
                                        } else {

                                            // dd($sell_decimal,"down".$up_current_btc_price); dd($randomDecimal, $down_current_btc_price);
                                            for ($i = 0; $i < $btc_sell_order_count; $i++) { //Alış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }

                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                //TODO: Burada Min > Max olduğu için hata veriyor. Diğerleri de kontrol edilecek. dd($up_current_btc_price,$sell_decimal, $old_price, $new_price); $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.'.$price_scale_count.'f',$up_current_btc_price * $div), sprintf('%.'.$price_scale_count.'f',$sell_decimal * $div)) / $div));
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                // $randomDecimal = (mt_rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        report($e);
                                    }
                                } else if (($old_price - $new_price) < 0) {
                                    $percent = abs(100 - $parity_diff);
                                    // dd($percent . " artıyor"); Emir Girişleri Komutu
                                    try {
                                        $randomDecimal = $current_price;
                                        $sell_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                        // dd($randomDecimal, $up_current_price);

                                        $up_current_btc_price = $sell_decimal + (($sell_decimal * $btc_sell_spread) / 100);

                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                        if (!empty($is_parity_orders_sell->toArray())) {
                                            $parity_orders_sell_count = $is_parity_orders_sell->count();
                                            if ($parity_orders_sell_count > $btc_sell_order_count) {
                                                for ($i = 0; $i < abs($parity_orders_sell_count - $btc_sell_order_count); $i++) {
                                                    $is_parity_orders_sell = collect($is_parity_orders_sell)->map(function ($item) {
                                                        return $item;
                                                    })->sortBy('price')->first();
                                                    Order::where('id', $is_parity_orders_sell->id)->delete();
                                                    $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                                }
                                            } else {
                                                for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                    $is_parity_orders_sell[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }

                                                for ($i = 0; $i < abs($btc_sell_order_count - $parity_orders_sell_count); $i++) {
                                                    if (
                                                        is_int($min_token) === true && is_int($max_token) === true
                                                    ) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                    }
                                                    // $randomDecimal = (mt_rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $amount = $orderAmount;
                                                    $price = $randomDecimal;
                                                    $total = $price * $amount;
                                                    $type = "limit";
                                                    $process = "sell";
                                                    Order::create([
                                                        'uuid' => Uuid::uuid4(),
                                                        'parities_id' => $parities_id,
                                                        'users_id' => $users_id,
                                                        'price' => $price,
                                                        'amount' => $amount,
                                                        'total' => $total,
                                                        'type' => $type,
                                                        'process' => $process,
                                                        'primary' => true,
                                                        'microtime' => str_replace(".", "", microtime(true))
                                                    ]);
                                                }
                                            }
                                            // dd($parity_orders_buy_count); //Null Check yapılacak
                                        } else {
                                            // dd($randomDecimal,$up_current_btc_price);
                                            for ($i = 0; $i < $btc_sell_order_count; $i++) { //Satış For Döngüsü
                                                if (
                                                    is_int($min_token) === true && is_int($max_token) === true
                                                ) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }

                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                // $randomDecimal = (mt_rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }

                                        $current_price = $market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']]['parity_price']['price']['value'];
                                        // $orderAmount = rand($min_token, $max_token);
                                        $randomDecimal = $current_price;
                                        $buy_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                        $down_current_btc_price = $buy_decimal - (($buy_decimal * $btc_buy_spread) / 100);

                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                        if (!empty($is_parity_orders_buy->toArray())) {

                                            $parity_orders_buy_count = $is_parity_orders_buy->count();
                                            if ($parity_orders_buy_count > $btc_buy_order_count) {
                                                for ($i = 0; $i < abs($parity_orders_buy_count - $btc_buy_order_count); $i++) {
                                                    $is_parity_orders_buy = collect($is_parity_orders_buy)->map(function ($item) {
                                                        return $item;
                                                    })->sortBy('price')->first();
                                                    Order::where('id', $is_parity_orders_buy->id)->delete();
                                                    $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                                }
                                            } else {
                                                for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));
                                                    }
                                                    // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                    $is_parity_orders_buy[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }

                                                for ($i = 0; $i < abs($btc_buy_order_count - $parity_orders_buy_count); $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));

                                                    // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $amount = $orderAmount;
                                                    $price = $randomDecimal;
                                                    $total = $price * $amount;
                                                    $type = "limit";
                                                    $process = "buy";
                                                    Order::create([
                                                        'uuid' => Uuid::uuid4(),
                                                        'parities_id' => $parities_id,
                                                        'users_id' => $users_id,
                                                        'price' => $price,
                                                        'amount' => $amount,
                                                        'total' => $total,
                                                        'type' => $type,
                                                        'process' => $process,
                                                        'primary' => true,
                                                        'microtime' => str_replace(".", "", microtime(true))
                                                    ]);
                                                }
                                            }
                                        } else {
                                            // dd($randomDecimal, $down_current_btc_price);
                                            for ($i = 0; $i < $btc_buy_order_count; $i++) { //Alış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }

                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));

                                                // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "buy";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        report($e);
                                    }
                                } else {
                                    try {
                                        $percent = abs(100 - $parity_diff);
                                        $randomDecimal = $current_price;
                                        
                                        $buy_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                        $down_current_btc_price = $buy_decimal - (($buy_decimal * $btc_buy_spread) / 100);

                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();

                                        if (!empty($is_parity_orders_buy->toArray())) {

                                            $parity_orders_buy_count = $is_parity_orders_buy->count();
                                            if ($parity_orders_buy_count > $btc_buy_order_count) {
                                                for ($i = 0; $i < abs($parity_orders_buy_count - $btc_buy_order_count); $i++) {
                                                    $is_parity_orders_buy = collect($is_parity_orders_buy)->map(function ($item) {
                                                        return $item;
                                                    })->sortBy('price')->first();
                                                    Order::where('id', $is_parity_orders_buy->id)->delete();
                                                    $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                                }
                                            } else {
                                                //Eski fiyatları güncelleme komutu
                                                for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                    if (($is_parity_orders_buy[$i]["price"] < $down_current_btc_price || $is_parity_orders_buy[$i]["price"] > $buy_decimal)) {
                                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                                            $orderAmount = rand($min_token, $max_token);
                                                        } else {
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                            // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));
                                                        }
                                                        // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                        $is_parity_orders_buy[$i]->update([
                                                            'price' => $randomDecimal,
                                                            'amount' => $orderAmount,
                                                            'total' => $randomDecimal * $orderAmount
                                                        ]);
                                                    }
                                                }

                                                //Ekleme Komutu
                                                for ($i = 0; $i < abs($btc_buy_order_count - $parity_orders_buy_count); $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));

                                                    // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $amount = $orderAmount;
                                                    $price = $randomDecimal;
                                                    $total = $price * $amount;
                                                    $type = "limit";
                                                    $process = "buy";
                                                    Order::create([
                                                        'uuid' => Uuid::uuid4(),
                                                        'parities_id' => $parities_id,
                                                        'users_id' => $users_id,
                                                        'price' => $price,
                                                        'amount' => $amount,
                                                        'total' => $total,
                                                        'type' => $type,
                                                        'process' => $process,
                                                        'primary' => true,
                                                        'microtime' => str_replace(".", "", microtime(true))
                                                    ]);
                                                }
                                            }
                                            // dd($parity_orders_buy_count); //Null Check yapılacak

                                        } else {
                                            // dd($randomDecimal, $up_current_btc_price);
                                            for ($i = 0; $i < $btc_buy_order_count; $i++) { //Satış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }

                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_btc_price * $div), sprintf('%.' . $price_scale_count . 'f', $buy_decimal * $div)) / $div));

                                                // $randomDecimal = (mt_rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "buy";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }

                                        $current_price = $market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']]['parity_price']['price']['value'];
                                        // $orderAmount = rand($min_token, $max_token);
                                        $randomDecimal = $current_price;
                                        $sell_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                        $up_current_btc_price = $sell_decimal + (($sell_decimal * $btc_sell_spread) / 100);

                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                        if (!empty($is_parity_orders_sell->toArray())) {

                                            $parity_orders_sell_count = $is_parity_orders_sell->count();
                                            if ($parity_orders_sell_count > $btc_sell_order_count) {
                                                for ($i = 0; $i < abs($parity_orders_sell_count - $btc_sell_order_count); $i++) {
                                                    $is_parity_orders_sell = collect($is_parity_orders_sell)->map(function ($item) {
                                                        return $item;
                                                    })->sortBy('price')->first();
                                                    Order::where('id', $is_parity_orders_sell->id)->delete();
                                                    $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                                }
                                            } else {
                                                //Eski Fiyatları Güncelleme Komutu

                                                for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                    if ($is_parity_orders_sell[$i]["price"] < $sell_decimal || $is_parity_orders_sell[$i]["price"] > $up_current_btc_price) {

                                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                                            $orderAmount = rand($min_token, $max_token);
                                                        } else {
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                            // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        }

                                                        $decimals = 10;
                                                        $div = pow(
                                                            10,
                                                            $decimals
                                                        );
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                        // $randomDecimal = (mt_rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                        $is_parity_orders_sell[$i]->update([
                                                            'price' => $randomDecimal,
                                                            'amount' => $orderAmount,
                                                            'total' => $randomDecimal * $orderAmount
                                                        ]);
                                                    }
                                                }
                                                //Ekleme Komutu
                                                for ($i = 0; $i < abs($btc_sell_order_count - $parity_orders_sell_count); $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    // $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.'.$price_scale_count.'f',$up_current_btc_price * $div), sprintf('%.'.$price_scale_count.'f',$sell_decimal * $div)) / $div));
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $amount = $orderAmount;
                                                    $price = $randomDecimal;
                                                    $total = $price * $amount;
                                                    $type = "limit";
                                                    $process = "sell";
                                                    Order::create([
                                                        'uuid' => Uuid::uuid4(),
                                                        'parities_id' => $parities_id,
                                                        'users_id' => $users_id,
                                                        'price' => $price,
                                                        'amount' => $amount,
                                                        'total' => $total,
                                                        'type' => $type,
                                                        'process' => $process,
                                                        'primary' => true,
                                                        'microtime' => str_replace(".", "", microtime(true))
                                                    ]);
                                                }
                                            }
                                        } else {

                                            // dd($sell_decimal,"down".$up_current_btc_price); dd($randomDecimal, $down_current_btc_price);
                                            for ($i = 0; $i < $btc_sell_order_count; $i++) { //Alış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }

                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                //TODO: Burada Min > Max olduğu için hata veriyor. Diğerleri de kontrol edilecek. dd($up_current_btc_price,$sell_decimal, $old_price, $new_price); $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.'.$price_scale_count.'f',$up_current_btc_price * $div), sprintf('%.'.$price_scale_count.'f',$sell_decimal * $div)) / $div));
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $sell_decimal * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_btc_price * $div)) / $div));

                                                // $randomDecimal = (mt_rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        report($e);
                                    }
                                }
                            } else {
                                dd('boş');
                            }
                        } else {
                            //TODO: BTC'siz Emirler $decimals = 22; // number of decimal places $div = pow(10, $decimals);

                            // // Syntax: mt_rand(min, max); $i = mt_rand(0.0000001 * $div, 0.0000005 * $div) / $div; dd($i);
                            if (($parities = ParityPrice::where('parities_id', $parities_id)) && $parities->get()->count() > 0) {

                                $parity_price = $parities->where('parities_id', $parities_id)->where('type', 'price')->where('source', 'local')->orderBy('id', 'DESC')->limit(2)->get();
                                $old_price = $parity_price->last()['value'];
                                $new_price = $parity_price->first()['value'];

                                if ($old_price == 0 || $new_price == 0) {

                                    throw new \Exception("Son fiyatlar 0 dan farklı olmalıdır.");
                                }
                                // dd($old_price,$new_price);
                                if ($old_price != $new_price) {

                                    try {
                                        $randomDecimal = $current_price;
                                        // dd($randomDecimal,$up_current_price,$down_current_price);
                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();

                                        // dd("ok");
                                        if (!empty($is_parity_orders_sell->toArray())) {

                                            $parity_orders_sell_count = $is_parity_orders_sell->count();
                                            if ($parity_orders_sell_count == $sell_order_count) {
                                                for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {

                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }

                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $current_price * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                    $is_parity_orders_sell[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }
                                            } else {
                                                for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }


                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $current_price * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                    $is_parity_orders_sell[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }
                                                //databasedeki order sayısı , market maker tablosundaki (yani istenen) order sayısından büyük ise
                                                if ($parity_orders_sell_count > $sell_order_count) {
                                                    // $is_parity_orders_sell = collect($is_parity_orders_sell)->map(function ($item) { return $item; })->sortBy('price')->take(abs($parity_orders_sell_count - $sell_order_count)); dd($is_parity_orders_sell);
                                                    for ($i = 0; $i < abs($parity_orders_sell_count - $sell_order_count); $i++) {
                                                        $is_parity_orders_sell = collect($is_parity_orders_sell)->map(function ($item) {
                                                            return $item;
                                                        })->sortBy('price')->first();
                                                        Order::where('id', $is_parity_orders_sell->id)->delete();
                                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                                    }
                                                } else {
                                                    for ($i = 0; $i < abs($sell_order_count - $parity_orders_sell_count); $i++) {
                                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                                            $orderAmount = rand($min_token, $max_token);
                                                        } else {
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                            // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        }


                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $current_price * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_price * $div)) / $div));

                                                        // $randomDecimal = (mt_rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                        $amount = $orderAmount;
                                                        $price = $randomDecimal;
                                                        $total = $price * $amount;
                                                        $type = "limit";
                                                        $process = "sell";
                                                        Order::create([
                                                            'uuid' => Uuid::uuid4(),
                                                            'parities_id' => $parities_id,
                                                            'users_id' => $users_id,
                                                            'price' => $price,
                                                            'amount' => $amount,
                                                            'total' => $total,
                                                            'type' => $type,
                                                            'process' => $process,
                                                            'primary' => true,
                                                            'microtime' => str_replace(".", "", microtime(true))
                                                        ]);
                                                    }
                                                }
                                            }
                                        } else {
                                            for ($i = 0; $i < $sell_order_count; $i++) { //Satış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }


                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $current_price * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_price * $div)) / $div));

                                                // $randomDecimal = (mt_rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }

                                        $current_price = $market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']]['parity_price']['price']['value'];
                                        // $orderAmount = rand($min_token, $max_token);
                                        $randomDecimal = $current_price;
                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                        // dd($is_parity_orders_buy);
                                        if (!empty($is_parity_orders_buy->toArray())) {
                                            $parity_orders_buy_count = $is_parity_orders_buy->count();
                                            if ($parity_orders_buy_count == $buy_order_count) {
                                                for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }


                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_price * $div), sprintf('%.' . $price_scale_count . 'f', $current_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($down_current_price * pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                    $is_parity_orders_buy[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }
                                            } else {
                                                for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                                        $orderAmount = rand($min_token, $max_token);
                                                    } else {
                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                        // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                    }


                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_price * $div), sprintf('%.' . $price_scale_count . 'f', $current_price * $div)) / $div));
                                                    // $randomDecimal = (mt_rand($down_current_price * pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));

                                                    $is_parity_orders_buy[$i]->update([
                                                        'price' => $randomDecimal,
                                                        'amount' => $orderAmount,
                                                        'total' => $randomDecimal * $orderAmount
                                                    ]);
                                                }
                                                if ($parity_orders_buy_count > $buy_order_count) {
                                                    for ($i = 0; $i < abs($parity_orders_buy_count - $buy_order_count); $i++) {
                                                        $is_parity_orders_buy = collect($is_parity_orders_buy)->map(function ($item) {
                                                            return $item;
                                                        })->sortBy('price')->first();
                                                        Order::where('id', $is_parity_orders_buy->id)->delete();
                                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                                    }
                                                } else {
                                                    for ($i = 0; $i < abs($buy_order_count - $parity_orders_buy_count); $i++) {
                                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                                            $orderAmount = rand($min_token, $max_token);
                                                        } else {
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                            // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        }


                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_price * $div), sprintf('%.' . $price_scale_count . 'f', $current_price * $div)) / $div));
                                                        // $randomDecimal = (mt_rand($down_current_price * pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                        $amount = $orderAmount;
                                                        $price = $randomDecimal;
                                                        $total = $price * $amount;
                                                        $type = "limit";
                                                        $process = "buy";
                                                        Order::create([
                                                            'uuid' => Uuid::uuid4(),
                                                            'parities_id' => $parities_id,
                                                            'users_id' => $users_id,
                                                            'price' => $price,
                                                            'amount' => $amount,
                                                            'total' => $total,
                                                            'type' => $type,
                                                            'process' => $process,
                                                            'primary' => true,
                                                            'microtime' => str_replace(".", "", microtime(true))
                                                        ]);
                                                    }
                                                }
                                            }
                                        } else {
                                            for ($i = 0; $i < $buy_order_count; $i++) { //Alış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }


                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_price * $div), sprintf('%.' . $price_scale_count . 'f', $current_price * $div)) / $div));
                                                // $randomDecimal = (mt_rand($down_current_price * pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "buy";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        report($e);
                                    }
                                }
                                //old price ve new price eşit değilse yani sabitse
                                else {
                                    try {
                                        $randomDecimal = $current_price;
                                        // dd($randomDecimal,$up_current_price,$down_current_price);
                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();

                                        // dd("ok");
                                        if (!empty($is_parity_orders_sell->toArray())) {

                                            $parity_orders_sell_count = $is_parity_orders_sell->count();
                                            if ($parity_orders_sell_count != $sell_order_count) {
                                                //databasedeki order sayısı , market maker tablosundaki (yani istenen) order sayısından büyük ise
                                                if ($parity_orders_sell_count > $sell_order_count) {
                                                    for ($i = 0; $i < abs($parity_orders_sell_count - $sell_order_count); $i++) {
                                                        $is_parity_orders_sell = collect($is_parity_orders_sell)->map(function ($item) {
                                                            return $item;
                                                        })->sortBy('price')->first();
                                                        Order::where('id', $is_parity_orders_sell->id)->delete();
                                                        $is_parity_orders_sell = Order::where('parities_id', $parities_id)->where('process', 'sell')->whereNull('deleted_at')->get();
                                                    }
                                                } else {
                                                    for ($i = 0; $i < abs($sell_order_count - $parity_orders_sell_count); $i++) {
                                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                                            $orderAmount = rand($min_token, $max_token);
                                                        } else {
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                            // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        }


                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $current_price * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_price * $div)) / $div));

                                                        // $randomDecimal = (mt_rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                        $amount = $orderAmount;
                                                        $price = $randomDecimal;
                                                        $total = $price * $amount;
                                                        $type = "limit";
                                                        $process = "sell";
                                                        Order::create([
                                                            'uuid' => Uuid::uuid4(),
                                                            'parities_id' => $parities_id,
                                                            'users_id' => $users_id,
                                                            'price' => $price,
                                                            'amount' => $amount,
                                                            'total' => $total,
                                                            'type' => $type,
                                                            'process' => $process,
                                                            'primary' => true,
                                                            'microtime' => str_replace(".", "", microtime(true))
                                                        ]);
                                                    }
                                                }
                                            }
                                        } else {
                                            for ($i = 0; $i < $sell_order_count; $i++) { //Satış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }


                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $current_price * $div), sprintf('%.' . $price_scale_count . 'f', $up_current_price * $div)) / $div));

                                                // $randomDecimal = (mt_rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }

                                        $current_price = $market[$bot_parities[$c]['parities']['source']['symbol'] . "-" . $bot_parities[$c]['parities']['coin']['symbol']]['parity_price']['price']['value'];
                                        // $orderAmount = rand($min_token, $max_token);
                                        $randomDecimal = $current_price;
                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                        // dd($is_parity_orders_buy);
                                        if (!empty($is_parity_orders_buy->toArray())) {
                                            $parity_orders_buy_count = $is_parity_orders_buy->count();
                                            if ($parity_orders_buy_count != $buy_order_count) {
                                                if ($parity_orders_buy_count > $buy_order_count) {
                                                    for ($i = 0; $i < abs($parity_orders_buy_count - $buy_order_count); $i++) {
                                                        $is_parity_orders_buy = collect($is_parity_orders_buy)->map(function ($item) {
                                                            return $item;
                                                        })->sortBy('price')->first();
                                                        Order::where('id', $is_parity_orders_buy->id)->delete();
                                                        $is_parity_orders_buy = Order::where('parities_id', $parities_id)->where('process', 'buy')->whereNull('deleted_at')->get();
                                                    }
                                                } else {
                                                    for ($i = 0; $i < abs($buy_order_count - $parity_orders_buy_count); $i++) {
                                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                                            $orderAmount = rand($min_token, $max_token);
                                                        } else {
                                                            $decimals = 10;
                                                            $div = pow(10, $decimals);
                                                            $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                            // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                        }


                                                        $decimals = 10;
                                                        $div = pow(10, $decimals);
                                                        $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_price * $div), sprintf('%.' . $price_scale_count . 'f', $current_price * $div)) / $div));
                                                        // $randomDecimal = (mt_rand($down_current_price * pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                        $amount = $orderAmount;
                                                        $price = $randomDecimal;
                                                        $total = $price * $amount;
                                                        $type = "limit";
                                                        $process = "buy";
                                                        Order::create([
                                                            'uuid' => Uuid::uuid4(),
                                                            'parities_id' => $parities_id,
                                                            'users_id' => $users_id,
                                                            'price' => $price,
                                                            'amount' => $amount,
                                                            'total' => $total,
                                                            'type' => $type,
                                                            'process' => $process,
                                                            'primary' => true,
                                                            'microtime' => str_replace(".", "", microtime(true))
                                                        ]);
                                                    }
                                                }
                                            }
                                        } else {
                                            for ($i = 0; $i < $buy_order_count; $i++) { //Alış For Döngüsü
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $decimals = 10;
                                                    $div = pow(10, $decimals);
                                                    $orderAmount = sprintf('%.' . $scale_count . 'f', mt_rand(sprintf('%.' . $scale_count . 'f', $min_token) * $div, sprintf('%.' . $scale_count . 'f', $max_token) * $div) / $div);
                                                    // $orderAmount = (mt_rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count));
                                                }


                                                $decimals = 10;
                                                $div = pow(10, $decimals);
                                                $randomDecimal = sprintf('%.' . $price_scale_count . 'f', (mt_rand(sprintf('%.' . $price_scale_count . 'f', $down_current_price * $div), sprintf('%.' . $price_scale_count . 'f', $current_price * $div)) / $div));
                                                // $randomDecimal = (mt_rand($down_current_price * pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count));
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "buy";
                                                Order::create([
                                                    'uuid' => Uuid::uuid4(),
                                                    'parities_id' => $parities_id,
                                                    'users_id' => $users_id,
                                                    'price' => $price,
                                                    'amount' => $amount,
                                                    'total' => $total,
                                                    'type' => $type,
                                                    'process' => $process,
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        report($e);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    dd('no');
                }
            }
        } catch (\Exception $e) {
            printf("MarketMaker ---" . $e->getMessage() . "\n\r");
        }
    }
}
