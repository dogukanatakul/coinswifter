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
            $spread = 20; //%'lik aralık olacak
            $order_count = 20; //20 adet emir girileceğini varsayalım
            $up_current_price = $current_price + ($current_price * $spread) / 100; 
            $down_current_price = $current_price - ($current_price * $spread) / 100; 
            $min_token = 50000;
            $max_token = 100000;
            // $orderAmount = rand($min_token,$max_token); //Emir'de girilecek token miktarı
            // $break_order = 5; //Emir'de girilecek token miktarının katlanma modülaritesi (a % b === 0)
            // $order_multiply = 3; //Emir'de girilecek token miktarının modülariteye göre kaç kat yüksek verileceği

            //Bitcoin endexli emir girişi isteğe bağlı olacak (Seç ya da seçme) parities_id'ye göre order var mı ? yok mu diye sorgu at. Eğer yoksa create at, varsa update at.
            if ($current_price !== 0) {

                if ($is_btc_base === true) {
                    //Bu id'deki coin paritesi var mı yok mu kontrolü yapılacak
                    if(($parities = ParityPrice::where('parities_id', 1)) && $parities->get()->count()> 0)
                    {
                        $parity_price = $parities->where('parities_id', 1)->where('type', 'price')->where('source', 'local')->orderBy('id', 'DESC')->limit(2)->get();
                        $old_price = $parity_price->last()['value'];
                        $new_price = $parity_price->first()['value'];
                        $parity_fark = (100/$old_price)* $new_price;
                        if(($old_price - $new_price) > 0 ){
                            $percent = 100 - $parity_fark;
                            // dd($percent . " lan sat gidiyor  a q qq");
                            
                            //Emir Girişleri Komutu

                            try {
                                $randomDecimal = $current_price;
                                $buy_decimal = $randomDecimal - (($randomDecimal * $percent) / 100);
                                $down_current_btc_price = $buy_decimal - (($buy_decimal * $spread) / 100);
                                
                                // dd($randomDecimal, $up_current_btc_price);
                                for ($i = 0; $i < $order_count; $i++) { //Satış For Döngüsü
                                    $orderAmount = rand($min_token, $max_token);
                                    $randomDecimal = rand($down_current_btc_price * pow(10,10), $buy_decimal * pow(10, 10)) / pow(10, 10);
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

                                $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                                // $orderAmount = rand($min_token, $max_token);
                                $randomDecimal = $current_price;
                                $sell_decimal = $randomDecimal - (($randomDecimal * $percent) / 100);
                                
                                $up_current_btc_price = $sell_decimal + (($sell_decimal * $spread) / 100);
                                // dd($sell_decimal,"down".$up_current_btc_price);
                                // dd($randomDecimal, $down_current_btc_price);
                                for ($i = 0; $i < $order_count; $i++) { //Alış For Döngüsü
                                    $orderAmount = rand($min_token, $max_token);

                                    $randomDecimal = rand($up_current_btc_price * pow(10, 10), $sell_decimal * pow(10, 10)) / pow(10, 10);
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
                            } catch (\Exception $e) {
                                report($e);
                            }


                        }else {
                            $percent = abs(100 - $parity_fark);
                            // dd($percent . " artıyor");

                            //Emir Girişleri Komutu
                            try {
                                $randomDecimal = $current_price;
                                $sell_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                // dd($randomDecimal, $up_current_price);

                                $up_current_btc_price = $sell_decimal + (($sell_decimal * $spread) / 100);
                                // dd($randomDecimal,$up_current_btc_price);
                                for ($i = 0; $i < $order_count; $i++) { //Satış For Döngüsü
                                    $orderAmount = rand($min_token, $max_token);

                                    $randomDecimal = rand($sell_decimal * pow(10, 10), $up_current_btc_price * pow(10, 10)) / pow(10, 10);
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

                                $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                                // $orderAmount = rand($min_token, $max_token);
                                $randomDecimal = $current_price;
                                $buy_decimal = $randomDecimal + (($randomDecimal * $percent) / 100);
                                $down_current_btc_price = $buy_decimal - (($buy_decimal * $spread) / 100);
                                // dd($randomDecimal, $down_current_btc_price);
                                for ($i = 0; $i < $order_count; $i++) { //Alış For Döngüsü
                                    $orderAmount = rand($min_token, $max_token);

                                    $randomDecimal = rand($down_current_btc_price * pow(10, 10), $buy_decimal * pow(10, 10)) / pow(10, 10);
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
                            } catch (\Exception $e) {
                                report($e);
                            }
                        }
                        
                    }
                    else
                    {
                        dd('boş');
                    }
                    
                    
                } else {
                    try {
                        $randomDecimal = $current_price;
                        // dd($randomDecimal,$up_current_price,$down_current_price);
                        for ($i = 0; $i < $order_count; $i++) { //Satış For Döngüsü
                            $orderAmount = rand($min_token, $max_token);
                            $randomDecimal = rand($current_price * pow(10, 10), $up_current_price * pow(10, 10)) / pow(10, 10);
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

                        $current_price = $market['TRY-USDJ']['parity_price']['price']['value'];
                        // $orderAmount = rand($min_token, $max_token);
                        $randomDecimal = $current_price;
                        for ($i = 0; $i < $order_count; $i++) { //Alış For Döngüsü
                            $orderAmount = rand($min_token, $max_token);
                            $randomDecimal = rand($down_current_price *  pow(10, 10), $current_price * pow(10, 10)) / pow(10, 10);
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
                    } catch (\Exception $e) {
                        report($e);
                    }
                }
            }
        } else {
            dd('no');
        }
    }
}
