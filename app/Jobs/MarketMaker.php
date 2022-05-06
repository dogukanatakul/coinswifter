<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Parity;
use App\Models\ParityPrice;
use Hamcrest\Number\OrderingComparison;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

use function PHPSTORM_META\type;

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
            //Parite çiftlerinin fiyatlarını çekme
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
            if (!empty($market['TRY-USDJ'])) {
                $is_btc_base = true; //BTC bazında emir oluşturulsun mu ?
                $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                $buy_spread = 20; //%'lik aralık olacak
                $sell_spread = 10; //%'lik aralık olacak
                $buy_order_count = 10; //Buy emir sayısı
                $sell_order_count = 20; //Sell emir sayısı
                $btc_buy_spread = 10;
                $btc_sell_spread = 20;
                $btc_buy_order_count = 20;
                $btc_sell_order_count = 10;
                $up_current_price = $current_price + ($current_price * $sell_spread) / 100;
                $down_current_price = $current_price - ($current_price * $buy_spread) / 100;
                $min_token = 1;
                $max_token = 10;
                $scale_count = 5;
                $price_scale_count = 10;
                // dd($current_price);
                //Bitcoin endexli emir girişi isteğe bağlı olacak (Seç ya da seçme) parities_id'ye göre order var mı ? yok mu diye sorgu at. Eğer yoksa create at, varsa update at.
                if ($current_price !== 0) {

                    if ($is_btc_base === true) {

                        //Bu id'deki coin paritesi var mı yok mu kontrolü yapılacak
                        if (($parities = ParityPrice::where('parities_id', 1)) && $parities->get()->count() > 0) {
                            $parity_price = $parities->where('parities_id', 1)->where('type', 'price')->where('source', 'local')->orderBy('id', 'DESC')->limit(2)->get();
                            $old_price = $parity_price->last()['value'];
                            $new_price = $parity_price->first()['value'];
                            // dd($old_price,$new_price);
                            // if ($old_price == 0 || $new_price == 0) {
                            //     throw new \Exception("Fiyat Gir Babuj");
                            //     // dd("Fiyat gir babuj");
                            // }
                            $parity_fark = (100 / $old_price) * $new_price;
                            if (($old_price - $new_price) > 0) {
                                $percent = 100 - $parity_fark;
                                // dd($percent . " lan sat gidiyor  a q qq");

                                //Emir Girişleri Komutu

                                try {
                                    $randomDecimal = $current_price;
                                    $buy_decimal = $randomDecimal - (($randomDecimal * $percent) / 100);
                                    $down_current_btc_price = $buy_decimal - (($buy_decimal * $btc_buy_spread) / 100);

                                    $is_parity_orders_buy = Order::where('parities_id', 7)->where('process', 'buy')->whereNull('deleted_at')->get();
                                    if (!empty($is_parity_orders_buy->toArray())) {

                                        $parity_orders_buy_count = $is_parity_orders_buy->count();
                                        if ($parity_orders_buy_count ==  $btc_buy_order_count) {
                                            for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

                                                $is_parity_orders_buy[$i]->update([
                                                    'price' => $randomDecimal,
                                                    'amount' => $orderAmount,
                                                    'total' => $randomDecimal * $orderAmount
                                                ]);
                                            }
                                        } else {
                                            //Eski fiyatları güncelleme komutu
                                            for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                                $parities_id = 3;
                                                $users_id = 1;
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
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
                                                $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                            }
                                            $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                            $parities_id = 3;
                                            $users_id = 1;
                                            $amount = $orderAmount;
                                            $price = $randomDecimal;
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
                                                'primary' => true,
                                                'microtime' => str_replace(".", "", microtime(true))
                                            ]);
                                        }
                                    }

                                    $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                                    // $orderAmount = rand($min_token, $max_token);
                                    $randomDecimal = $current_price;
                                    $sell_decimal = $randomDecimal - (($randomDecimal * $percent) / 100);

                                    $up_current_btc_price = $sell_decimal + (($sell_decimal * $btc_sell_spread) / 100);
                                    $is_parity_orders_sell = Order::where('parities_id', 7)->where('process', 'sell')->whereNull('deleted_at')->get();
                                    if (!empty($is_parity_orders_sell->toArray())) {

                                        $parity_orders_sell_count = $is_parity_orders_sell->count();
                                        if ($parity_orders_sell_count ==  $btc_sell_order_count) {
                                            for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                                // dd($sell_decimal,$up_current_btc_price);
                                                $parities_id = 3;
                                                $users_id = 1;
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $amount_pure = 100;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
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
                                                    'primary' => true,
                                                    'microtime' => str_replace(".", "", microtime(true))
                                                ]);
                                            }
                                        }
                                    } else {
                                        // dd($sell_decimal,"down".$up_current_btc_price);
                                        // dd($randomDecimal, $down_current_btc_price);
                                        for ($i = 0; $i < $btc_sell_order_count; $i++) { //Alış For Döngüsü
                                            if (is_int($min_token) === true && is_int($max_token) === true) {
                                                $orderAmount = rand($min_token, $max_token);
                                            } else {
                                                $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                            }
                                            $randomDecimal = rand($up_current_btc_price * pow(10, $price_scale_count), $sell_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                            // dd($sell_decimal,$up_current_btc_price);
                                            $parities_id = 3;
                                            $users_id = 1;
                                            $amount = $orderAmount;
                                            $price = $randomDecimal;
                                            $amount_pure = 100;
                                            $total = $price * $amount;
                                            $type = "limit";
                                            $process = "sell";
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
                                                'primary' => true,
                                                'microtime' => str_replace(".", "", microtime(true))
                                            ]);
                                        }
                                    }
                                } catch (\Exception $e) {
                                    report($e);
                                }
                            } else {
                                $percent = abs(100 - $parity_fark);
                                // dd($percent . " artıyor");

                                //Emir Girişleri Komutu
                                try {
                                    $randomDecimal = $current_price;
                                    $sell_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                    // dd($randomDecimal, $up_current_price);

                                    $up_current_btc_price = $sell_decimal + (($sell_decimal * $btc_sell_spread) / 100);

                                    $is_parity_orders_sell = Order::where('parities_id', 7)->where('process', 'sell')->whereNull('deleted_at')->get();
                                    if (!empty($is_parity_orders_sell->toArray())) {
                                        $parity_orders_sell_count = $is_parity_orders_sell->count();
                                        if ($parity_orders_sell_count ==  $btc_sell_order_count) {
                                            for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                                // dd($sell_decimal,$up_current_btc_price);
                                                $parities_id = 3;
                                                $users_id = 1;
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
                                                $amount_pure = 100;
                                                $total = $price * $amount;
                                                $type = "limit";
                                                $process = "sell";
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
                                                $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                            }
                                            $randomDecimal = rand($sell_decimal * pow(10, $price_scale_count), $up_current_btc_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                            // dd($sell_decimal,$up_current_btc_price);
                                            $parities_id = 3;
                                            $users_id = 1;
                                            $amount = $orderAmount;
                                            $price = $randomDecimal;
                                            $amount_pure = 100;
                                            $total = $price * $amount;
                                            $type = "limit";
                                            $process = "sell";
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
                                                'primary' => true,
                                                'microtime' => str_replace(".", "", microtime(true))
                                            ]);
                                        }
                                    }

                                    $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                                    // $orderAmount = rand($min_token, $max_token);
                                    $randomDecimal = $current_price;
                                    $buy_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                    $down_current_btc_price = $buy_decimal - (($buy_decimal * $btc_buy_spread) / 100);

                                    $is_parity_orders_buy = Order::where('parities_id', 7)->where('process', 'buy')->whereNull('deleted_at')->get();
                                    if (!empty($is_parity_orders_buy->toArray())) {

                                        $parity_orders_buy_count = $is_parity_orders_buy->count();
                                        if ($parity_orders_buy_count ==  $btc_buy_order_count) {
                                            for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                                if (is_int($min_token) === true && is_int($max_token) === true) {
                                                    $orderAmount = rand($min_token, $max_token);
                                                } else {
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                                    $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                                }
                                                $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                                $parities_id = 3;
                                                $users_id = 1;
                                                $amount = $orderAmount;
                                                $price = $randomDecimal;
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
                                                $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                            }
                                            $randomDecimal = rand($down_current_btc_price * pow(10, $price_scale_count), $buy_decimal * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                            $parities_id = 3;
                                            $users_id = 1;
                                            $amount = $orderAmount;
                                            $price = $randomDecimal;
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
                        try {
                            $randomDecimal = $current_price;
                            // dd($randomDecimal,$up_current_price,$down_current_price);
                            $is_parity_orders_sell = Order::where('parities_id', 7)->where('process', 'sell')->whereNull('deleted_at')->get();
                            if (!empty($is_parity_orders_sell->toArray())) {

                                $parity_orders_sell_count = $is_parity_orders_sell->count();
                                if ($parity_orders_sell_count ==  $sell_order_count) {
                                    for ($i = 0; $i < $parity_orders_sell_count; $i++) {
                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                            $orderAmount = rand($min_token, $max_token);
                                        } else {
                                            $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                        }
                                        $randomDecimal = rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                            $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                        }
                                        $randomDecimal = rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

                                        $is_parity_orders_sell[$i]->update([
                                            'price' => $randomDecimal,
                                            'amount' => $orderAmount,
                                            'total' => $randomDecimal * $orderAmount
                                        ]);
                                    }

                                    for ($i = 0; $i < abs($sell_order_count - $parity_orders_sell_count); $i++) {
                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                            $orderAmount = rand($min_token, $max_token);
                                        } else {
                                            $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                        }
                                        $randomDecimal = rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                        $parities_id = 3;
                                        $users_id = 1;
                                        $amount = $orderAmount;
                                        $price = $randomDecimal;
                                        $amount_pure = 100;
                                        $total = $price * $amount;
                                        $type = "limit";
                                        $process = "sell";
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
                                            'primary' => true,
                                            'microtime' => str_replace(".", "", microtime(true))
                                        ]);
                                    }
                                }
                            } else {
                                for ($i = 0; $i < $sell_order_count; $i++) { //Satış For Döngüsü
                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                        $orderAmount = rand($min_token, $max_token);
                                    } else {
                                        $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                    }
                                    $randomDecimal = rand($current_price * pow(10, $price_scale_count), $up_current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                    $parities_id = 3;
                                    $users_id = 1;
                                    $amount = $orderAmount;
                                    $price = $randomDecimal;
                                    $amount_pure = 100;
                                    $total = $price * $amount;
                                    $type = "limit";
                                    $process = "sell";
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
                                        'primary' => true,
                                        'microtime' => str_replace(".", "", microtime(true))
                                    ]);
                                }
                            }

                            $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                            // $orderAmount = rand($min_token, $max_token);
                            $randomDecimal = $current_price;
                            $is_parity_orders_buy = Order::where('parities_id', 7)->where('process', 'buy')->whereNull('deleted_at')->get();
                            // dd($is_parity_orders_buy);
                            if (!empty($is_parity_orders_buy->toArray())) {
                                $parity_orders_buy_count = $is_parity_orders_buy->count();
                                if ($parity_orders_buy_count ==  $buy_order_count) {
                                    for ($i = 0; $i < $parity_orders_buy_count; $i++) {
                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                            $orderAmount = rand($min_token, $max_token);
                                        } else {
                                            $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                        }
                                        $randomDecimal = rand($down_current_price *  pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

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
                                            $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                        }
                                        $randomDecimal = rand($down_current_price *  pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);

                                        $is_parity_orders_buy[$i]->update([
                                            'price' => $randomDecimal,
                                            'amount' => $orderAmount,
                                            'total' => $randomDecimal * $orderAmount
                                        ]);
                                    }

                                    for ($i = 0; $i < abs($buy_order_count - $parity_orders_buy_count); $i++) {
                                        if (is_int($min_token) === true && is_int($max_token) === true) {
                                            $orderAmount = rand($min_token, $max_token);
                                        } else {
                                            $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                        }
                                        $randomDecimal = rand($down_current_price *  pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                        $parities_id = 3;
                                        $users_id = 1;
                                        $amount = $orderAmount;
                                        $price = $randomDecimal;
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
                                            'primary' => true,
                                            'microtime' => str_replace(".", "", microtime(true))
                                        ]);
                                    }
                                }
                            } else {
                                for ($i = 0; $i < $buy_order_count; $i++) { //Alış For Döngüsü
                                    if (is_int($min_token) === true && is_int($max_token) === true) {
                                        $orderAmount = rand($min_token, $max_token);
                                    } else {
                                        $orderAmount = rand($min_token * pow(10, $scale_count), $max_token * pow(10, $scale_count)) / pow(10, $scale_count);
                                    }
                                    $randomDecimal = rand($down_current_price *  pow(10, $price_scale_count), $current_price * pow(10, $price_scale_count)) / pow(10, $price_scale_count);
                                    $parities_id = 3;
                                    $users_id = 1;
                                    $amount = $orderAmount;
                                    $price = $randomDecimal;
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
            } else {
                dd('no');
            }
        } catch (\Exception $e) {
            printf("MarketMaker ---" . "Fiyat Gir babuj" . "\n\r");
            // $this->queueData(['status' => 'fail', 'message' => $e->getMessage()]);
            // throw new \Exception($e);
        }
        
    }
}
