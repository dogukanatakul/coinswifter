<?php

namespace App\Http\Controllers\Api\CoinActions;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use App\Models\Commission;
use App\Models\LogActivity;
use App\Models\NodeTransaction;
use App\Models\OrderTransaction;
use App\Models\Order;
use App\Models\ParityChart;
use App\Models\UserCoin;
use App\Models\UserFavoritePairs;
use App\Models\Parity;
use App\Models\ParityPrice;
use App\Models\UserWithdrawalWallet;
use App\Models\UserWithdrawalWalletChild;
use App\Models\UserWithdrawalWalletFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class Exchange extends Controller
{

    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->session()->has('user')) {
                $this->user = $request->session()->get('user');
            } else {
                $this->user = false;
            }
            return $next($request);
        });
    }


    public function tokens(): \Illuminate\Http\JsonResponse
    {
        $market = Parity::with(['source', 'coin', 'parity_price', 'commission', 'user_favorite' => function ($q) {
            if ($this->user) {
                $q->where('users_id', $this->user->id);
            }
        }])
            ->whereHas('commission')
            ->orderBy('order', 'ASC')
            ->get();

        $market = collect($market)->mapWithKeys(function ($item, $key) {
            $newItem = $item->toArray();

            if (empty($item->user_favorite)) {
                $newItem['user_favorite'] = false;
            } else {
                $newItem['user_favorite'] = true;
            }

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
        return response()->json([
            'status' => 'success',
            'data' => $market,
        ]);
    }


    public function chart($parity, $chartTime = "15m"): bool|array
    {
        $chart = Cache::remember('chart' . $parity . $chartTime, now()->addSeconds(15), function () use ($parity, $chartTime) {
            return ParityChart::where('parities_id', $parity)->where('type', $chartTime)->orderBy('id', 'DESC')->first();
        });
        return empty($chart) ? false : $chart->data;
    }

    public function setParity(Request $request, $source, $coin): \Illuminate\Http\JsonResponse
    {
        if (count($checkCoin = Coin::whereIn('symbol', [$source, $coin])->get()->groupBy('symbol')) == 2) {
            if (!empty($checkParite = Parity::where('source_coin_id', $checkCoin[$source]->first()->id)->where('coin_id', $checkCoin[$coin]->first()->id)->first())) {
                $sellOrders = Order::select(['price', 'amount', 'process'])
                    ->where('price', '!=', 0)
                    ->where('parities_id', $checkParite->id)
                    ->where('process', 'sell')
                    ->orderBy('price', 'DESC')
                    ->get()
                    ->take(-10)
                    ->groupBy('price');
                $buyOrders = Order::select(['price', 'amount', 'process'])
                    ->where('price', '!=', 0)
                    ->where('parities_id', $checkParite->id)
                    ->orderBy('price', 'DESC')
                    ->where('process', 'buy')
                    ->limit(10)
                    ->get()
                    ->groupBy('price');
                $lastPrice = OrderTransaction::where('parities_id', $checkParite->id)
                    ->orderBy('id', 'DESC')
                    ->first();

                $parityChanges = ParityPrice::where('parities_id', $checkParite->id)->where('type', 'price')->first()->price ?? 0;

                $status = [
                    'price' => priceFormat($lastPrice->price ?? $parityChanges),
                    'buy_price' => !empty($buyOrders->first()) ? priceFormat($buyOrders->first()->first()->price ?? 0) : 0,
                    'sell_price' => !empty($sellOrders->first()) ? priceFormat($sellOrders->last()->first()->price ?? 0) : 0
                ];

                $sellOrders = collect($sellOrders)->map(function ($data, $key) {
                    return [
                        'price' => priceFormat($key),
                        'amount' => priceFormat(decimal_sum($data->pluck('amount')->toArray())),
                        'total' => priceFormat(\Litipk\BigNumbers\Decimal::fromString(priceFormat($key))->mul(\Litipk\BigNumbers\Decimal::fromString(priceFormat(decimal_sum($data->pluck('amount')->toArray()))), null)->innerValue())
                    ];
                });

                $buyOrders = collect($buyOrders)->map(function ($data, $key) {
                    return [
                        'price' => priceFormat($key),
                        'amount' => priceFormat(decimal_sum($data->pluck('amount')->toArray())),
                        'total' => priceFormat(\Litipk\BigNumbers\Decimal::fromString(priceFormat($key))->mul(\Litipk\BigNumbers\Decimal::fromString(priceFormat(decimal_sum($data->pluck('amount')->toArray()))), null)->innerValue())
                    ];
                });

                if ($sellOrders->count() === 0 && $buyOrders->count() === 0 && $status['price'] > 0) {
                    $orders = [
                        'buy' => [],
                        'sell' => [],
                    ];
                    for ($i = 1; $i <= 8; $i++) {
                        $orders['buy'][] = [
                            'price' => priceFormat((floatval($status['price']) / 1000) * (1000 - rand($i, $i + 0. . rand(55, 99)))),
                            'amount' => priceFormat(0 . "." . rand(111111, 999999999)),
                        ];
                    }
                    for ($i = 8; $i >= 1; $i--) {
                        $orders['sell'][] = [
                            'price' => priceFormat((floatval($status['price']) / 1000) * (1000 - rand($i, $i + 0. . rand(55, 99)))),
                            'amount' => priceFormat(0 . "." . rand(111111, 999999999)),
                        ];
                    }
                } else {
                    $orders = [
                        'buy' => array_values($buyOrders->toArray()),
                        'sell' => array_values($sellOrders->toArray()),
                    ];
                }

                $lastOperations = OrderTransaction::select('price', 'amount', 'created_at')
                    ->where('parities_id', $checkParite->id)
                    ->limit(25)
                    ->orderBy('id', 'DESC')
                    ->get();
                if ($lastOperations->count() == 0 && $status['price'] > 0) {
                    $lastOperations = [];
                    for ($i = 1; $i <= 13; $i++) {
                        $lastOperations[] = [
                            'price' => priceFormat((floatval($status['price']) / 1000) * (1000 - rand($i, $i + 0.99))),
                            'amount' => priceFormat(0 . "." . rand(111111, 999999999)),
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                } else {
                    $lastOperations = collect($lastOperations)->map(function ($data, $key) {
                        return [
                            'price' => priceFormat($data->price),
                            'amount' => priceFormat($data->amount),
                            'created_at' => $data->created_at->format('Y-m-d H:i')
                        ];
                    })->toArray();
                }


                if ($this->user) {
                    $wallet = new \App\Http\Controllers\Api\CoinActions\WalletController();
                    $wallet = $wallet->balanceAndOrders(false, $this->user);
                    if (!$wallet) {
                        return response()->json([
                            'status' => 'fail',
                            'error_key' => 'login',
                            'message' => __('api_messages.user_check_fail_message')
                        ]);
                    }
                    $wallet = [
                        'source' => [
                            'symbol' => $wallet[$source]['symbol'],
                            'balance' => $wallet[$source]['balance'],
                            'locked' => $wallet[$source]['locked'],
                        ],
                        'coin' => [
                            'symbol' => $wallet[$coin]['symbol'],
                            'balance' => $wallet[$coin]['balance'],
                            'locked' => $wallet[$coin]['locked'],
                        ],
                    ];
                    $myOrders = Parity::with([
                        'orders' => function ($query) {
                            $query->with([
                                'buying_trades',
                                'selling_trades'
                            ])
                                ->where('users_id', $this->user->id)
                                ->orderBy('deleted_at', 'DESC')
                                ->orderBy('amount', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->withTrashed();
                        },
                        'source',
                        'coin'
                    ])
                        ->find($checkParite->id);

                    if (empty($myOrders) || $myOrders->orders->count() == 0) {
                        $myOrders = false;
                    } else {
                        $myOrders = $myOrders->orders->map(function ($data) {
                            $newData = $data->toArray();
                            $newData['price'] = priceFormat($newData['price']);
                            $newData['amount'] = priceFormat($newData['amount']);
                            $newData['amount_pure'] = priceFormat($newData['amount_pure']);
                            $newData['trigger'] = priceFormat($newData['trigger']);
                            $newData['created_at'] = $data->created_at->format('Y-m-d H:i');
                            $newData['operation'] = __('api_messages.' . $data->process);
                            $newData['finished'] = $data->buying_trades->sum('amount') + $data->selling_trades->sum('amount');
                            $newData['percent'] = intval((1 - (floatval($newData['amount']) / (floatval($newData['finished']) + floatval($newData['amount'])))) * 100);
                            $newData['is_deleted'] = !empty($data->deleted_at);
                            return $newData;
                        })->toArray();
                    }
                } else {
                    $wallet = false;
                    $myOrders = false;
                }
                return response()->json([
                    'status' => 'success',
                    'data' => $orders,
                    'chart' => $this->chart($checkParite->id, $request->chartParts),
                    'marketStatus' => $status,
                    'wallet' => $wallet,
                    'myOrders' => $myOrders,
                    'lastOperations' => $lastOperations,
                ]);
            }
        }
        return response()->json([
            'status' => 'fail',
            'message' => __('api_messages.form_parameter_fail_message')
        ]);
    }

    public function setFavorite(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'coin' => 'required|filled|string|exists:App\Models\Coin,symbol',
            'source' => 'required|filled|string|exists:App\Models\Coin,symbol'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        } else {
            $coins = Coin::whereIn('symbol', [$request->coin, $request->source])->get()->groupBy('symbol');
            if (!empty($parity = Parity::where('source_coin_id', $coins[$request->source]->first()->id)->where('coin_id', $coins[$request->coin]->first()->id)->first())) {

                if (!empty($favorite = UserFavoritePairs::where('users_id', $this->user->id)->where('parities_id', $parity->id)->first())) {
                    $favorite->forceDelete();
                    return response()->json([
                        'status' => 'success',
                        'message' => __('api_messages.favorite_delete_success_message')
                    ]);
                } else {
                    try {
                        UserFavoritePairs::create([
                            'users_id' => $this->user->id,
                            'parities_id' => $parity->id,
                        ]);
                        return response()->json([
                            'status' => 'success',
                            'message' => __('api_messages.favorite_set_success_message')
                        ]);
                    } catch (\Throwable $e) {
                        report($e);
                        return response()->json([
                            'status' => 'fail',
                            'message' => __('api_messages.form_parameter_fail_message')
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.form_parameter_fail_message')
                ]);
            }
        }
    }


    public function test()
    {


//        dd("ok");
//        $user = User::where('username', 'dogukanatakul')->first()->makeVisible(['id'])->toArray();
//        $bot = new \App\Jobs\WalletCreate($user, 0);
//        dd($bot->handle());

//        dd(NodeTransaction::where('txh', '796e8bfabd256ff31ffa4c0d49b960066e383f9d672a9599e5c7c2450ea3d118')->orderBy('block_number', 'ASC')->get()->toArray());

//        $txh = NodeTransaction::where('txh', '0x536fbf1134583aa75967f6941c3d5138418df9fb5e56fe9872e2ed35f7e6cb51')->first();
//        dd($txh->toArray());


//        dd(Decimal::fromString("2.19994364799943648", null)->innerValue());

//        dd(\Litipk\BigNumbers\Decimal::fromString('2.19994364799943648')->div(\Litipk\BigNumbers\Decimal::fromString("1"), null)->innerValue());
//        dd(\Litipk\BigNumbers\Decimal::fromInteger(5)->comp(\Litipk\BigNumbers\Decimal::fromInteger(1)));
//
//
//        $randWalletControl = UserWallet::with([
//            'user_coin' => function ($q) {
//                $q->with([
//                    'coin'
//                ]);
//            }
//        ])
//            ->inRandomOrder()
//            ->first();
//        dd($randWalletControl->toArray());

        $bot = new \App\Jobs\ParityPrice();
//        $bot = new \App\Jobs\NodeTransaction();
        dd($bot->handle());


        UserCoin::where('coins_id', '!=', 1)->update([
            'balance' => 0,
            'balance_pure' => 0,
        ]);

        UserCoin::where('coins_id', 1)->update([
            'balance' => 10000000,
            'balance_pure' => 0,
        ]);
        NodeTransaction::where('value', '>', 0)->update([
            'processed' => 0,
        ]);
//        NodeTransaction::truncate();
        Commission::truncate();
        OrderTransaction::truncate();
        Order::truncate();
        UserWithdrawalWalletFee::truncate();
        UserWithdrawalWalletChild::truncate();
        UserWithdrawalWallet::truncate();
        LogActivity::truncate();
        dd("ok");
//        dd("ok");
//        $wallets = NodeTransaction::get()->toArray();
//        dd(json_encode($wallets));


    }

}
