<?php

namespace App\Http\Controllers\Api\CoinActions;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use App\Models\CuzdanTanim;
use App\Models\Order;
use App\Models\User;
use App\Models\Parity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;

class CoinsController extends Controller
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


    public function deleteOrder($uuid): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            if (!empty($order = Order::where('users_id', $this->user->id)->where('uuid', $uuid)->first())) {
                $order->delete();
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => __('api_messages.delete_order_success_message')
            ]);
        } catch (\Throwable $e) {
            report($e);
            DB::rollBack();
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.delete_order_fail_message')
            ]);
        }
    }


    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function order(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = validator()->make(request()->all(), [
            'type' => 'required|filled|string',
            'action' => 'required|filled|string',
            'form' => 'required|filled',
            'form.amount' => 'required|filled|numeric',
            'form.percent' => 'required|filled|integer',
            'form.total' => 'required|filled|numeric',
            'selectedCoin.source.symbol' => 'required|filled|string|exists:App\Models\Coin,symbol',
            'selectedCoin.coin.symbol' => 'required|filled|string|exists:App\Models\Coin,symbol',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.form_filled_fail_message')
            ]);
        }
        // Cüzdan Kontrol
        $coins = Coin::whereIn('symbol', [$request->selectedCoin['source']['symbol'], $request->selectedCoin['coin']['symbol']])->get()->groupBy('id');

        $coins = collect($coins)->mapWithKeys(function ($item) use ($request) {
            return [($request->selectedCoin['source']['symbol'] === $item->first()->symbol ? 'source' : 'coin') => $item->first()];
        });
        //\

        // Durum Kontrol
        if ($request->action == 'sell' && $coins['coin']->status !== 'normal') {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
        //\

        // Parite kontrolü
        if (empty($parityControl = Parity::where('source_coin_id', $coins['source']->id)->where('coin_id', $coins['coin']->id)->first())) {
            return response()->json([
                'status' => 'fail',
                'message' => __('api_messages.system_fail_message')
            ]);
        }
        //\


        $wallet = new \App\Http\Controllers\Api\CoinActions\WalletController();
        $wallet = $wallet->balanceAndOrders(false, $this->user);
        $wallet = $wallet[$request->selectedCoin[$request->action === "buy" ? 'source' : 'coin']['symbol']];
        if ($request->type == "limit") {
            $validator = validator()->make(request()->all(), [
                'form.price' => 'required|filled|numeric|min:0000000000000000.0000000000000000000001|max:9999999999999999.9999999999999999999999',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.form_filled_fail_message')
                ]);
            }
            // Bakiye yeterlilik kontrolü
            if ($request->action === 'sell') {
                $totalOrderAmount = floatval($request->form['amount']);
            } else if ($request->action === 'buy') {
                $totalOrderAmount = floatval(floatval($request->form['amount']) * floatval($request->form['price']));
            } else {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.system_fail_message')
                ]);
            }
            if ($wallet['balance'] < $totalOrderAmount) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.transfer_fail_balance_message')
                ]);
            }
            //\
            try {
                Order::create([
                    'parities_id' => $parityControl->id,
                    'users_id' => $this->user->id,
                    'price' => floatval($request->form['price']),
                    'amount' => floatval($request->form['amount']),
                    'percent' => floatval($request->form['percent']),
                    'total' => floatval($request->form['total']),
                    'microtime' => str_replace(".", "", microtime(true)),
                    'type' => $request->type,
                    'process' => $request->action
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => __('api_messages.order_success_message')
                ]);
            } catch (\Throwable $e) {
                report($e);
                return response()->json([
                    'status' => 'success',
                    'message' => __('api_messages.system_fail_message')
                ]);
            }

        } else if ($request->type == "market") {
            // Bakiye yeterlilik kontrolü
            if ($request->action == 'sell') {
                $totalOrderAmount = floatval($request->form['amount']);
            } else {
                $totalOrderAmount = floatval($request->form['total']);
            }

            if (floatval($wallet['balance']) < $totalOrderAmount) {
                return response()->json([
                    'status' => 'fail',
                    'message' => __('api_messages.transfer_fail_balance_message')
                ]);
            }
            try {
                Order::create([
                    'parities_id' => $parityControl->id,
                    'users_id' => $this->user->id,
                    'amount' => floatval($request->form['amount']),
                    'percent' => floatval($request->form['percent']),
                    'total' => floatval($request->form['total']),
                    'microtime' => str_replace(".", "", microtime(true)),
                    'type' => $request->type,
                    'process' => $request->action
                ]);
                return response()->json([
                    'status' => 'success',
                    'message' => __('api_messages.order_success_message')
                ]);
            } catch (\Throwable $e) {
                report($e);
                return response()->json([
                    'status' => 'success',
                    'message' => __('api_messages.system_fail_message')
                ]);
            }
        }
        return response()->json([
            'status' => 'fail',
            'message' => __('api_messages.form_parameter_fail_message')
        ]);
    }
}
