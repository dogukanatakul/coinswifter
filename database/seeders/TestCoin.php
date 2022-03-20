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
        $alterCoins = [
            [
                'name' => 'Türk Lirası',
                'symbol' => 'TRY',
                'networks_id' => $network['SOURCE']->first()->id,
                'contract' => null,
                'commission_type' => 'static',
                'commission_in' => 5,
                'commission_out' => 5,
            ],
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
                'token_type' => 'erc20',
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
                'token_type' => 'bep20'
            ],


            [
                'name' => 'TRON',
                'symbol' => 'TRX',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['TRX']->first()->id,
            ],
            [
                'name' => "JUST GOV",
                'symbol' => 'JST',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['TRX']->first()->id,
                'contract' => 'TF17BgPaZYbz8oxbjhriubPDsA7ArKoLX3',
                'token_type' => 'trc20'
            ],
            [
                'name' => "JUST Stablecoin",
                'symbol' => 'USDJ',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['TRX']->first()->id,
                'contract' => 'TLBaRhANQoJFTqre9Nf1mjuwNWjCJeYqUL',
                'token_type' => 'trc20'
            ],
            [
                'name' => "TRONZ",
                'symbol' => 'TRZ',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['TRX']->first()->id,
                'contract' => '1000016',
                'token_type' => 'trc10'
            ],
        ];

        foreach ($alterCoins as $alterCoin) {
            $alterCoin['transfer_min'] = 0;
            \App\Models\Coin::create($alterCoin);
        }

        $coins = \App\Models\Coin::all()->groupBy('symbol');

        $parities = [
            'TRY' => [
                'ETH',
                'BNB',
                'TRX',
                'AS',
                'RoR',
                'JST',
                'USDJ',
            ],
            'ETH' => [
                'BNB',
                'AS',
                'RoR',
                'JST',
                'USDJ'
            ],
            'USDJ' => [
                'ETH',
                'BNB',
                'AS',
                'RoR',
                'TRX',
                'JST',
                'USDJ'
            ],
        ];

        foreach ($parities as $index => $parity) {
            foreach ($parity as $item) {
                $parity = \App\Models\Parity::create([
                    'source_coin_id' => $coins[$index]->first()->id,
                    'coin_id' => $coins[$item]->first()->id,
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
}
