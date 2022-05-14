<?php

namespace App\Http\Controllers\Api\CoinActions;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use App\Models\Network;
use App\Models\UserBank;
use App\Models\UserCoin;
use App\Models\UserWallet;
use App\Models\UserWithdrawal;
use App\Models\UserWithdrawalWallet;
use App\Models\UserWithdrawalWalletChild;
use App\Models\UserWithdrawalWalletFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WalletController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (session()->has('user')) {
                $this->user = session()->get('user');
            } else {
                $this->user = false;
            }
            return $next($request);
        });
    }

    public function withdrawalWallet(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'coin' => 'required|filled|string|exists:App\Models\Coin,symbol',
            'wallet' => 'required|filled|string',
            'amount' => 'required|filled|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message'),
            ]);
        }
        $coin = $this->balanceAndOrders(true)[$request->coin] ?? false;
        if (!($coin && \Litipk\BigNumbers\Decimal::fromString($coin['balance'])->comp(\Litipk\BigNumbers\Decimal::fromString($request->amount)) >= 0 && \Litipk\BigNumbers\Decimal::fromString($coin['transfer_max'])->comp(\Litipk\BigNumbers\Decimal::fromString($request->amount)) >= 0 && \Litipk\BigNumbers\Decimal::fromString($coin['transfer_min'])->comp(\Litipk\BigNumbers\Decimal::fromString($request->amount)) <= 0)) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.transfer_fail_balance_message'),
            ]);
        }

        $transferAmount = $request->amount;
        if ($coin['commission_type'] === 'percent') {
            $transferCommission = \Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($transferAmount)->mul(\Litipk\BigNumbers\Decimal::fromString($coin['commission_out']), null)->innerValue())->div(\Litipk\BigNumbers\Decimal::fromString("100"), null)->innerValue();
        } else {
            $transferCommission = $coin['commission_out'];
        }
        $transferAmount = \Litipk\BigNumbers\Decimal::fromString($transferAmount)->sub(\Litipk\BigNumbers\Decimal::fromString($transferCommission), null)->innerValue();

        DB::beginTransaction();
        try {
            if (!empty($checkWallet = UserWallet::with(['user_coin'])->whereHas('user_coin', function ($q) use ($coin) {
                $q->where('coins_id', $coin['coins_id']);
            })->where('wallet', trim($request->wallet))->first())) {
                $checkWallet = $checkWallet->users_id;
            } else {
                $checkWallet = null;
            }
            $insertUserWithdrawalWallet = UserWithdrawalWallet::create([
                'users_id' => $this->user->id,
                'to_user_id' => $checkWallet,
                'user_coins_id' => $coin['user_coins_id'],
                'coins_id' => $coin['coins_id'],
                'amount' => $request->amount,
                'send_amount' => $transferAmount,
                'commission' => $transferCommission,
                'to' => $request->wallet
            ]);
            if (empty($checkWallet)) {
                // Fee Coin ID
                $coinID = Coin::whereNull('contract')->where('networks_id', $coin['networks_id'])->first()->id;
                $network = Network::where('id', $coin['networks_id'])->first();
                //\

                // TRANSFER YAPABİLECEK CÜZDANLAR
                $destinationTransferWallets = UserCoin::with([
                    'user_coin' => function ($q) use ($coinID) {
                        $q->where('coins_id', $coinID);
                    },
                    'user_withdrawal_wallet_child.user_withdrawal_wallet_fee'
                ])
                    ->whereHas('user_coin', function ($q) use ($coinID, $network) {
                        $q->where('coins_id', $coinID)->where('balance_pure', '>=', $network->fee);
                    });
                $destinationTransferWallets = $destinationTransferWallets->where('coins_id', $coin['coins_id'])
                    ->orderBy('balance_pure', 'ASC')
                    ->get();
                //\

                $destinationTransferWallets = collect($destinationTransferWallets)->map(function ($item) use ($network, $coinID, $coin) {
                    // Mevcut emirleri bakiyeden düşür
                    $item->balance_pure = \Litipk\BigNumbers\Decimal::fromString($item->balance_pure)->sub(\Litipk\BigNumbers\Decimal::fromString(decimal_sum($item->user_withdrawal_wallet_child->pluck('amount')->toArray())), null)->innerValue();
                    //\
                    $feeAmount = "0";
                    if ($item->user_withdrawal_wallet_child->count() > 0) {
                        foreach ($item->user_withdrawal_wallet_child as $user_withdrawal_wallet_child) {
                            // Eğer ana coinse fee'leride bakiyeden düşür
                            if ($coinID == $coin['coins_id']) {
                                $item->balance_pure = \Litipk\BigNumbers\Decimal::fromString($item->balance_pure)->sub(\Litipk\BigNumbers\Decimal::fromString($user_withdrawal_wallet_child->user_withdrawal_wallet_fee->amount), null)->innerValue();
                            }
                            //\
                            $feeAmount = \Litipk\BigNumbers\Decimal::fromString($feeAmount)->add(\Litipk\BigNumbers\Decimal::fromString($user_withdrawal_wallet_child->user_withdrawal_wallet_fee->amount), null)->innerValue();
                        }
                        $feeAmount = \Litipk\BigNumbers\Decimal::fromString($feeAmount)->add(\Litipk\BigNumbers\Decimal::fromString($network->fee), null)->innerValue();
                    }
                    // TOKEN'sa ve fee ödeyemiyorsa false döndür
                    if ($coinID != $coin['coins_id'] && \Litipk\BigNumbers\Decimal::fromString($item->user_coin->balance_pure)->comp(\Litipk\BigNumbers\Decimal::fromString($feeAmount)) < 0) {
                        return false;
                    }
                    //\
                    return $item;
                })->filter(function ($q) {
                    // bakiyesi 0 olanları sil
                    return $q->balance_pure !== "0";
                })->sortBy('balance_pure');

                // tek seferde gönderim yapabilecek cüzdanlar varsa ayıkla yoksa bakiyesi yüksekten düşüğe doğru sırala
                if (\Litipk\BigNumbers\Decimal::fromString($destinationTransferWallets->last()->balance_pure)->comp(\Litipk\BigNumbers\Decimal::fromString($transferAmount)) == 1) {
                    $destinationTransferWallets = collect($destinationTransferWallets)->filter(function ($q) use ($transferAmount, $network) {
                        return \Litipk\BigNumbers\Decimal::fromString($q->balance_pure)->comp(\Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($transferAmount)->add(\Litipk\BigNumbers\Decimal::fromString($network->fee), null)->innerValue())) >= 0;
                    });
                } else {
                    $destinationTransferWallets = $destinationTransferWallets->reverse();
                }
                $transferList = [];
                foreach ($destinationTransferWallets as $destinationTransferWallet) {
                    if (\Litipk\BigNumbers\Decimal::fromString($transferAmount)->comp(\Litipk\BigNumbers\Decimal::fromInteger(0)) > 0) {
                        if ($coin['contract']) {
                            $balance_pure = $destinationTransferWallet->balance_pure;
                        } else {
                            $balance_pure = \Litipk\BigNumbers\Decimal::fromString($destinationTransferWallet->balance_pure)->sub(\Litipk\BigNumbers\Decimal::fromString($network->fee), null)->innerValue();
                        }
                        if (\Litipk\BigNumbers\Decimal::fromString($balance_pure)->comp(\Litipk\BigNumbers\Decimal::fromInteger(0), null) >= 0) {
                            // devam
                            if (\Litipk\BigNumbers\Decimal::fromString(\Litipk\BigNumbers\Decimal::fromString($transferAmount)->sub(\Litipk\BigNumbers\Decimal::fromString($balance_pure), null)->innerValue())->comp(\Litipk\BigNumbers\Decimal::fromInteger(0)) > 0) {
                                $transferList[] = [
                                    'id' => $destinationTransferWallet->id,
                                    'amount' => $balance_pure,
                                ];
                                $transferAmount = \Litipk\BigNumbers\Decimal::fromString($transferAmount)->sub(\Litipk\BigNumbers\Decimal::fromString($balance_pure), null)->innerValue();
                            } else {
                                $transferList[] = [
                                    'id' => $destinationTransferWallet->id,
                                    'amount' => $transferAmount,
                                ];
                                $transferAmount = \Litipk\BigNumbers\Decimal::fromString($transferAmount)->sub(\Litipk\BigNumbers\Decimal::fromString($transferAmount), null)->innerValue();
                            }
                        }
                    }
                }

                foreach ($transferList as $item) {
                    $userWithdrawalWalletChild = UserWithdrawalWalletChild::create([
                        'user_withdrawal_wallets_id' => $insertUserWithdrawalWallet->id,
                        'user_coins_id' => $item['id'],
                        'amount' => $item['amount']
                    ]);
                    UserWithdrawalWalletFee::create([
                        'user_withdrawal_wallets_id' => $insertUserWithdrawalWallet->id,
                        'user_withdrawal_wallet_children_id' => $userWithdrawalWalletChild->id,
                        'amount' => $network->fee,
                    ]);
                }

                if (\Litipk\BigNumbers\Decimal::fromString($transferAmount)->comp(\Litipk\BigNumbers\Decimal::fromInteger(0), null) > 0 || count($transferList) > 5) {
                    $insertUserWithdrawalWallet->status = 3;
                    $insertUserWithdrawalWallet->save();
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.transfer_success_message')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message'),
                'code' => ''
            ]);
        }
    }

    public function deleteWithdrawalWallet(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'uuid' => 'required|filled|string|exists:App\Models\UserWithdrawalWallet,uuid',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_parameter_fail_message')
            ]);
        }
        $withdrawalWallet = UserWithdrawalWallet::with(['user_withdrawal_wallet_child'])
            ->where('users_id', $this->user->id)
            ->where('uuid', $request->uuid)
            ->where('status', 0)
            ->where('created_at', '>', now()->tz('Europe/Istanbul')->subMinutes(1)->toDateTimeLocalString())
            ->first();
        if (empty($withdrawalWallet)) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.withdrawal_cancel_fail_message')
            ]);
        } else {
            $withdrawalWallet->delete();
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.withdrawal_cancel_success_message')
            ]);
        }


    }


    public function balanceAndOrders($hidden = false, $user = null): array|bool
    {
        if (!empty($user)) {
            $this->user = $user;
        }
        try {
            $list = Coin::with([
                'user_coin' => function ($q) {
                    $q->with([
                        'parity_sources' => function ($q) {
                            $q->with(['orders' => function ($q) {
                                $q->where('users_id', $this->user->id)->where('process', 'buy');
                            }]);
                        },
                        'parity_coins' => function ($q) {
                            $q->with([
                                'orders' => function ($q) {
                                    $q->where('users_id', $this->user->id)->where('process', 'sell');
                                },
                                'parity_price' => function ($q) {
                                    $q->where('type', 'price')->orderBy('id', 'DESC');
                                }
                            ]);
                        },
                        'user_withdrawal_wallet' => function ($q) {
                            $q->where('users_id', $this->user->id)->orderBy('id', 'DESC');
                        },
                        'user_withdrawal' => function ($q) {
                            $q->with(['user_bank' => function ($q) {
                                $q->with(['bank']);
                            }])->where('users_id', $this->user->id)->orderBy('id', 'DESC');
                        },
                        'user_wallet'
                    ])->where('users_id', $this->user->id);
                },
                'network',
                'parity_coin' => function ($q) {
                    $q->with(['parity_price', 'source']);
                }
            ])->orderBy('order', 'ASC')->get();

            return collect($list)->mapWithKeys(function ($data) use ($hidden, $user) {
                $newData = [];
                $newData['transfer_min'] = priceFormat($data->transfer_min);
                $newData['transfer_max'] = priceFormat($data->transfer_max);
                $newData['commission_in'] = priceFormat($data->commission_in);
                $newData['commission_out'] = priceFormat($data->commission_out);
                $newData['commission_type'] = $data->commission_type;
                $newData['contract'] = empty($data->contract) ? false : $data->contract;
                if ($hidden) {
                    $newData['user_coins_id'] = $data->user_coin->id;
                    $newData['coins_id'] = $data->id;
                    $newData['networks_id'] = $data->networks_id;
                }
                $newData['balance'] = $data->user_coin->balance;
                $newData['locked'] = '0';
                $newData['symbol'] = $data->symbol;
                $newData['name'] = $data->name;
                $newData['price'] = 0;
                // Transfer Talepleri
                $newData['user_withdrawal_wallet'] = [];
                if (!empty($data->user_coin->user_withdrawal_wallet)) {
                    foreach ($data->user_coin->user_withdrawal_wallet as $user_withdrawal_wallet) {
                        if ($user_withdrawal_wallet->status === 0) {
                            $newData['locked'] = \Litipk\BigNumbers\Decimal::fromString($newData['locked'])->add(\Litipk\BigNumbers\Decimal::fromString($user_withdrawal_wallet->amount), $data->precision)->innerValue();
                        }
                        $newData['user_withdrawal_wallet'][] = [
                            'uuid' => $user_withdrawal_wallet->uuid,
                            'amount' => priceFormat($user_withdrawal_wallet->amount),
                            'send_amount' => priceFormat($user_withdrawal_wallet->send_amount),
                            'commission' => priceFormat($user_withdrawal_wallet->commission),
                            'to' => $user_withdrawal_wallet->to,
                            'status' => $user_withdrawal_wallet->status === 0 ? (\Carbon\Carbon::parse($user_withdrawal_wallet->created_at->format('Y-m-d H:i:s'))->diffInMinutes(now()->tz('Europe/Istanbul')) > 1 ? __('api_messages.processing') : __('api_messages.waiting')) : ($user_withdrawal_wallet->status == 3 ? __('api_messages.denied') : __('api_messages.approved')),
                            'created_at' => $user_withdrawal_wallet->created_at->format('Y-m-d H:i:s'),
                            'cancel' => !(\Carbon\Carbon::parse($user_withdrawal_wallet->created_at->format('Y-m-d H:i:s'))->diffInMinutes(now()->tz('Europe/Istanbul')) > 1)
                        ];
                    }
                }

                // Çekim Talepleri
                $newData['user_withdrawal'] = [];
                if (!empty($data->user_coin->user_withdrawal)) {
                    foreach ($data->user_coin->user_withdrawal as $user_withdrawal) {
                        if ($user_withdrawal->status === 0) {
                            $newData['locked'] = \Litipk\BigNumbers\Decimal::fromString($newData['locked'])->add(\Litipk\BigNumbers\Decimal::fromString($user_withdrawal->amount), $data->precision)->innerValue();
                        }
                        $newData['user_withdrawal'][] = [
                            'amount' => $user_withdrawal->amount,
                            'status' => $user_withdrawal->status === 0 ? __('api_messages.waiting') : ($user_withdrawal->status === 1 ? __('api_messages.approved') : __('api_messages.denied')),
                            'bank' => $user_withdrawal->user_bank->bank->name,
                            'iban' => $user_withdrawal->user_bank->iban
                        ];
                    }
                }
                // Emirler
                $newData['orders'] = [];

                foreach ($data->user_coin->parity_coins as $parity) {
                    if ($parity->orders->count() > 0) {
                        $newData['orders'] = array_merge($newData['orders'], $parity->orders->map(function ($item) use ($parity) {
                            $newData = $item->toArray();
                            $newData['created_at'] = $item->created_at->format('Y-m-d');
                            $newData['parity'] = implode("/", [$parity->coin->symbol, $parity->source->symbol]);
                            $newData['operation'] = __('api_messages.' . $item->process);
                            $newData['finished'] = \Litipk\BigNumbers\Decimal::fromString(decimal_sum($item->buying_trades->pluck('amount')->toArray()))->add(\Litipk\BigNumbers\Decimal::fromString(decimal_sum($item->selling_trades->pluck('amount')->toArray())), null)->innerValue();
                            $newData['percent'] = priceFormat((1 - ($newData['amount'] / (floatval($newData['finished']) + floatval($newData['amount'])))) * 100, "float", 2);
                            $newData['is_deleted'] = !empty($item->deleted_at);
                            return $newData;
                        })->toArray());
                        $newData['locked'] = \Litipk\BigNumbers\Decimal::fromString($newData['locked'])->add(\Litipk\BigNumbers\Decimal::fromString(decimal_sum($parity->orders->pluck('amount')->toArray())), $data->precision)->innerValue();

                    }
                }

                foreach ($data->user_coin->parity_sources as $parity) {
                    if ($parity->orders->count() > 0) {
                        $newData['orders'] = array_merge($newData['orders'], $parity->orders->map(function ($item) use ($parity) {
                            $newData = $item->toArray();
                            $newData['created_at'] = $item->created_at->format('Y-m-d');
                            $newData['parity'] = implode("/", [$parity->coin->symbol, $parity->source->symbol]);
                            $newData['operation'] = __('api_messages.' . $item->process);
                            $newData['finished'] = \Litipk\BigNumbers\Decimal::fromString(decimal_sum($item->buying_trades->pluck('amount')->toArray()))->add(\Litipk\BigNumbers\Decimal::fromString(decimal_sum($item->selling_trades->pluck('amount')->toArray())), null)->innerValue();
                            $newData['percent'] = priceFormat((1 - ($newData['amount'] / (floatval($newData['finished']) + floatval($newData['amount'])))) * 100, "float", 2);
                            $newData['is_deleted'] = !empty($item->deleted_at);
                            return $newData;
                        })->toArray());
                        $newData['locked'] = \Litipk\BigNumbers\Decimal::fromString($newData['locked'])->add(\Litipk\BigNumbers\Decimal::fromString(decimal_sum($parity->orders->pluck('total')->toArray())), $data->precision)->innerValue();
                    }
                }


                // Emirler
                $newData['network'] = [
                    'name' => $data->network->name,
                    'short_name' => $data->network->short_name
                ];
                $newData['wallet_code'] = dcdWllt(json_decode(dcdData($data->user_coin->user_wallet->wallet))) ?? null;
                $newData['total_balance'] = priceFormat($newData['balance']);

                $newData['prices'] = [];
                foreach ($data->parity_coin as $parity_coin) {
                    if ($parity_coin->parity_price->count() > 0) {
                        $newData['prices'][$parity_coin->source->symbol] = $parity_coin->parity_price->filter(function ($item) {
                            return $item->type === 'price';
                        })->first()->value;
                    }
                }
                $newData['balance'] = \Litipk\BigNumbers\Decimal::fromString($newData['balance'])->sub(\Litipk\BigNumbers\Decimal::fromString($newData['locked']), $data->precision)->innerValue();
                $newData['balance'] = priceFormat($newData['balance']);
                $newData['locked'] = priceFormat($newData['locked']);
                return [$data->symbol => $newData];
            })->toArray();
        } catch (\Exception $e) {
            report($e);
            return false;
        }

    }


    public function myWallets(): \Illuminate\Http\JsonResponse
    {
        $list = $this->balanceAndOrders();
        if ($list) {
            $totalMount = [
                'total' => priceFormat(collect($list)->map(function ($item) {
                    if ($item['symbol'] === 'TRY') {
                        return $item['balance'];
                    } else {
                        return isset($item['prices']['TRY']) ? $item['prices']['TRY'] * $item['balance'] : 0;
                    }
                })->sum()),
                'list' => collect($list)->filter(function ($item) {
                    if ($item['balance'] > 0) {
                        return $item;
                    }
                    return false;
                }),
                'donut' => collect($list)->map(function ($item, $key) {
                    if ($item['symbol'] === 'TRY') {
                        return $item['balance'];
                    } else {
                        return isset($item['prices']['TRY']) ? $item['prices']['TRY'] * $item['balance'] : 0;
                    }
                })
            ];
            return response()->json([
                'status' => 'success',
                'data' => $list,
                'total' => $totalMount
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'error_key' => 'login',
                'message' => __('api_messages.user_check_fail_message')
            ]);
        }
    }

    public function withdrawal(Request $request): \Illuminate\Http\JsonResponse
    {
        if (Cache::has('kyc-' . $this->user->id) && Cache::get('kyc-' . $this->user->id)) {
            if (!empty($coin = Coin::where('symbol', 'TRY')->first())) {
                if (!empty($userBank = UserBank::where('users_id', $this->user->id)->where('primary', true)->first())) {
                    $wallet = (object)$this->balanceAndOrders(true)[$coin->symbol];
                    if ($request->amount >= $coin->transfer_min && $request->amount <= $coin->transfer_max && $wallet->balance >= $request->amount) {
                        try {
                            UserWithdrawal::create([
                                'users_id' => $this->user->id,
                                'user_banks_id' => $userBank->id,
                                'user_coins_id' => $wallet->user_coins_id,
                                'amount' => $request->amount
                            ]);
                            return response()->json([
                                'status' => 'success',
                                'message' => __('api_messages.withdrawal_request_success_message')
                            ]);
                        } catch (\Throwable $e) {
                            report($e);
                            return response()->json([
                                'status' => 'fail',
                                'message' => __('api_messages.system_fail_message')
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => 'fail',
                            'message' => __('api_messages.withdrawal_request_amount_fail_message')
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'message' => __('api_messages.withdrawal_request_bank_fail_message')
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.form_filled_fail_message')
                ]);
            }
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.withdrawal_request_kyc_fail_message')
            ]);
        }
    }
}
