<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestCoin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $network = \App\Models\Network::get()->groupBy('short_name');

        $try = \App\Models\Coin::create([
            'name' => 'Türk Lirası',
            'symbol' => 'TRY',
            'networks_id' => $network['SOURCE']->first()->id,
            'contract' => null,
            'commission_type' => 'static',
            'commission_in' => 5,
            'commission_out' => 5,
        ]);

        $alterCoins = [
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
            ],
            [
                'name' => "RaySoft'un Yükselişi",
                'symbol' => 'RoR',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x6205BC98087fd43aEfA65336E1980868a3C7B87c',
            ],
            [
                'name' => 'Binance Coin',
                'symbol' => 'BNB',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
            ],
            [
                'name' => "AyrancıSalih",
                'symbol' => 'AS',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x862f029d9398493206852f8Cac348Afb25737f19',
            ],
//            [
//                'name' => "BaklavamNerede",
//                'symbol' => 'SİK',
//                'commission_in' => 0.25,
//                'commission_out' => 0.25,
//                'commission_type' => 'percent',
//                'status' => 'normal',
//                'networks_id' => $network['BSC']->first()->id,
//                'contract' => '0xbed785cc782E64849bA24321128ad77c00c17447',
//            ],
        ];

        foreach ($alterCoins as $alterCoin) {
            $alterCoin['transfer_min'] = 0;
            $coin = \App\Models\Coin::create($alterCoin);
            $parity = \App\Models\Parity::create([
                'source_coin_id' => $try->id,
                'coin_id' => $coin->id,
                'status' => 'normal',
                'settings' => [
                    'trading_market' => false,
                ],
            ]);

            \App\Models\ParityCommission::create([
                'parities_id' => $parity->id,
                'commission' => $alterCoin['commission_out'],
                'type' => 0,
            ]);
        }
    }
}
