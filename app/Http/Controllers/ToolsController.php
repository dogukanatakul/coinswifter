<?php

namespace App\Http\Controllers;


use App\Models\Bank;
use App\Models\Coin;
use App\Models\ContractedBank;
use App\Models\CustomBlock;
use App\Models\Network;
use App\Models\NodeCustomBlock;
use App\Models\NodeTransaction;
use App\Models\UserDeposit;
use App\Models\UserWallet;
use App\Models\UserWithdrawalWalletChild;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ToolsController extends Controller
{

//    public function insertFromJson()
//    {
//        $fields = [
//            'created_at',
//            'updated_at',
//            'deleted_at',
//            'islem_kilidi',
//            'onay_tarihi',
//            'red_tarihi',
//        ];
//        $tables = json_decode(Storage::disk('public')->get('backup.json'), true);
//        foreach ($tables as $table => $items) {
//            foreach ($items as $item) {
//                $item = collect($item)->map(function ($item, $key) use ($fields) {
//                    if (in_array($key, $fields) && (!empty($item))) {
//                        $item = \Carbon\Carbon::parse($item);
//                        return $item->format('Y-m-d H:i');
//                    } else {
//                        return $item;
//                    }
//                })->toArray();
//                \Illuminate\Support\Facades\DB::table($table)->insert($item);
//            }
//        }
//    }
//
//    public function backUp()
//    {
//
//        $tables = [
//            'kullanici_tanim',
//            'kullanici_iletisim',
//            'kullanici_banka_tanim',
//            'kullanici_referans',
//            'kullanici_adres',
//            'kullanici_adres',
//            'coin_listeleme_talepleri',
////          'kullanici_pariteler',
//            'kullanici_kyc',
//            'kullanici_sozlesme',
////          'cuzdan_tanim',
////          'emirler',
////          'emir_islemler',
////          'transferler',
////          'komisyonlar',
////          'node_logs',
//        ];
//
//        $data = [];
//        foreach ($tables as $table) {
//            $data[$table] = \Illuminate\Support\Facades\DB::table($table)->get()->toArray();
//        }
//
//        $data = collect($data)->filter(function ($item, $key) {
//            return count($item) > 0;
//        })->toJson();
//        Storage::disk('public')->put('backup.json', $data);
//        dd($data);
//    }

    public function walletList($key, $network, $json = true)
    {
        if ($key === "27ba4f9a-8bee-49ed-a945-b90d4b89a874") {
            $network = Network::where('short_name', $network)->first();
            if (!empty($network)) {
                $wallets = UserWallet::select(['wallet'])->where('networks_id', $network->id)->get()->pluck('wallet');
                if ($json) {
                    return response()->json($wallets->toArray());
                } else {
                    return $wallets->toArray();
                }
            } else {
                abort(404);
            }
        }
        abort(404);
    }

    public function setTransactions(Request $request, $network): string
    {
        $wallets = $this->walletList("27ba4f9a-8bee-49ed-a945-b90d4b89a874", $network, false);
        foreach ($request->toArray() as $item) {
            $item['progress'] = in_array($item['from'], $wallets) ? 'out' : 'in';
            try {
                NodeTransaction::create($item);
            } catch (\Exception $e) {
                report($e);
            }
        }
        return "success";
    }

    public function getTransactions($network): \Illuminate\Http\JsonResponse
    {
        $txhs = UserWithdrawalWalletChild::with(['user_coin.coin.network'])
            ->where('status', 2)
            ->whereHas('user_coin.coin.network', function ($q) use ($network) {
                $q->where('short_name', $network);
            })
            ->get()
            ->pluck('txh');
        return response()->json($txhs);
    }

    public function setBlocks(Request $request): string
    {
        foreach ($request->toArray() as $block) {
            try {
                NodeCustomBlock::create($block);
            } catch (\Exception $e) {
                report($e);
            }
        }
        return "success";
    }

    public function getBlocks($network): \Illuminate\Http\JsonResponse
    {
        $blocks = NodeCustomBlock::where('network', $network)->get()->pluck('block_number');
        return response()->json($blocks);
    }

    public function randomWallets($type): \Illuminate\Http\JsonResponse
    {
        $cacheWallets = [];
        if (Cache::has('tron_wallet_searching_' . $type)) {
            $cacheWallets = Cache::get('tron_wallet_searching_' . $type);
        }
        $wallet = UserWallet::whereHas('network', function ($q) {
            $q->where('short_name', 'TRX');
        })
            ->whereNotIn('wallet', $cacheWallets)
            ->orderBy('id', 'ASC')
            ->first();
        if (empty($wallet)) {
            $cacheWallets = [];
            $wallet = UserWallet::whereHas('network', function ($q) {
                $q->where('short_name', 'TRX');
            })
                ->whereNotIn('wallet', $cacheWallets)
                ->orderBy('id', 'ASC')
                ->first();
        }
        $txh = NodeTransaction::orWhere('from', 'ilike', $wallet->wallet)
            ->orWhere('to', 'ilike', $wallet->wallet)
            ->where('network', 'TRX')
            ->get();
        $cacheWallets[] = $wallet->wallet;
        Cache::put('tron_wallet_searching_' . $type, $cacheWallets);
        return response()->json([
            'wallet' => $wallet->wallet,
            'txh' => $txh->count() > 0 ? $txh->pluck('txh') : []
        ]);
    }

    public function setDeposits($swift, Request $request): string
    {
        $bank = ContractedBank::with(['bank'])->whereHas('bank', function ($q) use ($swift) {
            $q->where('swift', $swift);
        })->first();
        if (!empty($bank)) {
            foreach ($request->toArray() as $deposit) {
                $deposit['contracted_banks_id'] = $bank->id;
                $deposit['coins_id'] = Coin::where('symbol', 'TRY')->first()->id;
                try {
                    UserDeposit::create($deposit);
                } catch (\Exception $e) {
                    report($e);
                }
            }
        }
        return "success";
    }

}
