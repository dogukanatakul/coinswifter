<?php

namespace App\Http\Controllers\Api\CoinActions;

use App\Http\Controllers\Controller;
use App\Jobs\WalletCreate;
use App\Models\Coin;
use App\Models\OrderTransaction;
use App\Models\Order;
use App\Models\User;
use App\Models\UserFavoritePairs;
use App\Models\Parity;
use App\Models\ParityPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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
            $newItem['commission'] = priceFormat($item['commission']['commission'], "float");
            return [$item['source']['symbol'] . "-" . $item['coin']['symbol'] => $newItem];
        })->toArray();
        return response()->json([
            'status' => 'success',
            'data' => $market,
        ]);
    }


    public function chart($parity, $chartTime = "4h"): bool|array
    {
        $totalGet = 0;
        if ($chartTime === "1h") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(20)->format('Y-m-d H:i:s');
            $interval = "PT60M";
        } else if ($chartTime === "4h") {
            $subDate = now()->tz('Europe/Istanbul')->subHour(80)->format('Y-m-d H:i:s');
            $interval = "PT240M";
        } else if ($chartTime === "1d") {
            $subDate = now()->tz('Europe/Istanbul')->subDay(20)->format('Y-m-d H:i:s');
            $interval = "P1D";
        } else if ($chartTime === "1w") {
            $subDate = now()->tz('Europe/Istanbul')->subWeek(20)->format('Y-m-d H:i:s');
            $interval = "P7D";
        } else if ($chartTime === "1m") {
            $subDate = now()->tz('Europe/Istanbul')->subMonth(20)->format('Y-m-d H:i:s');
            $interval = "P1M";
        }

        // PT15M - PT60M - P4H - P1D - 1M

        $periods = new \DatePeriod(
            new \DateTime($subDate),
            new \DateInterval($interval),
            new \DateTime(now()->tz('Europe/Istanbul')->format('Y-m-d H:i:s'))
        );

        $list = [];
        $lastDate = false;
        foreach ($periods as $key => $value) {
            if ($subDate !== $value->format('Y-m-d H:i:s')) {
                $lastDate = $value->format('Y-m-d H:i:s');
                $swap = OrderTransaction::where('created_at', '>=', $subDate)
                    ->where('created_at', '<=', $value->format('Y-m-d H:i:s'))
                    ->where('parities_id', $parity)
                    ->get();
                if ($swap->count()) {
                    $totalGet += 1;
                }
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
            if ($swap->count()) {
                $totalGet += 1;
            }
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

        return $totalGet > 0 ? array_reverse($list) : false;
    }


    public function setParity($source, $coin): \Illuminate\Http\JsonResponse
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
                        'price' => priceFormat($key, "float"),
                        'amount' => priceFormat($data->sum('amount'), "float")
                    ];
                });

                $buyOrders = collect($buyOrders)->map(function ($data, $key) {
                    return [
                        'price' => priceFormat($key, "float"),
                        'amount' => priceFormat($data->sum('amount'), "float")
                    ];
                });
                if ($sellOrders->count() === 0 && $buyOrders->count() === 0 && $status['price'] > 0) {
                    $orders = [
                        'buy' => [],
                        'sell' => [
                            [
                                'price' => priceFormat(floatval($status['price'])),
                                'amount' => priceFormat(0 . "." . rand(111111, 999999999)),
                            ],
                        ],
                    ];
                    for ($i = 1; $i <= 8; $i++) {
                        $orders['buy'][] = [
                            'price' => priceFormat((floatval($status['price']) / 1000) * (1000 - rand($i, $i + 0.99))),
                            'amount' => priceFormat(0 . "." . rand(111111, 999999999)),
                        ];
                        $orders['sell'][] = [
                            'price' => priceFormat((floatval($status['price']) / 1000) * (1000 + rand($i, $i + 0.99))),
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
                    ->get()
                    ->toArray();

                if (count($lastOperations) === 0 && $status['price'] > 0) {
                    $lastOperations = [];
                    for ($i = 1; $i <= 13; $i++) {
                        $lastOperations[] = [
                            'price' => priceFormat((floatval($status['price']) / 1000) * (1000 - rand($i, $i + 0.99))),
                            'amount' => priceFormat(0 . "." . rand(111111, 999999999)),
                            'created_at' => date('Y-m-d H:i:s'),
                        ];
                    }
                }

                if ($this->user) {
                    $wallet = new \App\Http\Controllers\Api\CoinActions\WalletController();
                    $wallet = $wallet->balanceAndOrders(false, $this->user);
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
                    $myOrders = Parity::with(['orders' => function ($query) {
                        $query->with(['buying_trades', 'selling_trades'])
                            ->where('users_id', $this->user->id)->orderBy('id', 'DESC')
                            ->withTrashed();
                    }, 'source', 'coin'])->find($checkParite->id);

                    if (empty($myOrders) || $myOrders->orders->count() == 0) {
                        $myOrders = false;
                    } else {
                        $myOrders = $myOrders->orders->map(function ($data) {
                            $newData = $data->toArray();
                            $newData['price'] = priceFormat($newData['price'], "float");
                            $newData['amount'] = priceFormat($newData['amount'], "float");
                            $newData['trigger'] = priceFormat($newData['trigger'], "float");
                            $newData['created_at'] = $data->created_at->format('Y-m-d H:i');
                            $newData['operation'] = __('api_messages.' . $data->process);
                            $newData['finished'] = $data->buying_trades->sum('amount') + $data->selling_trades->sum('amount');
                            $newData['percent'] = priceFormat((1 - ($newData['amount'] / ($newData['finished'] + $newData['amount']))) * 100, "float", 2);
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
                    'chart' => $this->chart($checkParite->id),
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

    public function estimateGas($parity)
    {
        $orders = DB::select("SELECT t.id,t.miktar,c.cuzdan_kodu,pk.komisyon from emirler AS t
    LEFT JOIN parite_ciftleri AS p ON p.id = t.parite_ciftleri_id
    LEFT JOIN parite_komisyonlar AS pk ON p.id = pk.parite_ciftleri_id
    LEFT JOIN cuzdan_tanim AS c ON c.coin_id = p.coin_id AND c.kullanici_id=t.kullanici_id
    WHERE EXISTS (SELECT * FROM kullanici_tanim AS k WHERE t.kullanici_id = k.id AND k.islem_kilidi is null AND k.deleted_at is NULL)
    AND t.parite_ciftleri_id = 2 AND t.miktar > 0
    AND t.islem = 'sell'
    AND t.miktar+coalesce((select SUM(t.miktar) from emirler
                            WHERE t.miktar<t.miktar
                            AND (t.miktar=t.miktar AND t.id < t.id)),0) <= 300
                                    AND t.deleted_at is NULL
    order BY t.fiyat ASC, t.microTime ASC");
        $gasBnb = 0;
        foreach ($orders as $order) {
            $comission = floatval(($order->miktar / 100) * floatval($order->komisyon));
            $amount = floatval($order->miktar - $comission);
            if (($query = bscActions('fee_calculator', [
                    'from_address' => $order->cuzdan_kodu,
                    'to_address' => $order->cuzdan_kodu,
                    'value' => $amount,

                ])) && $query->status) {
                $gasBnb += floatval($query->content->bnb);
            } else {
                return "ok";
            }
            if (($query = bscActions('fee_calculator', [
                    'from_address' => $order->cuzdan_kodu,
                    'to_address' => $order->cuzdan_kodu,
                    'value' => $comission,

                ])) && $query->status) {
                $gasBnb += floatval($query->content->bnb);
            } else {
                return "ok";
            }
        }

        dd($gasBnb);
    }


    public function test()
    {


//        $users = User::get()->makeVisible(['id']);
//        foreach ($users as $user) {
//            WalletCreate::dispatch($user->toArray(), 0);
//        }
//        dd("ok");

        $bot = new \App\Jobs\NodeTransaction();
//        $bot = new \App\Jobs\FakeOrder();
        dd($bot->handle());
    }

}
