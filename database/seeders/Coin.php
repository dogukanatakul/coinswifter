<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Coin extends Seeder
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
                'name' => 'Binance Coin',
                'symbol' => 'BNB',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
            ],
            [
                'name' => 'Broovs',
                'symbol' => 'BRS',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x98C6fD0281A9A0300cB88553Bf386a3492bb70F7',
            ],
            [
                'name' => 'PI5',
                'symbol' => 'PI5',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xaEc1CDdB9963607F99f233d1A90E3A4Ea3e13023',
            ],
            [
                'name' => 'Pitbull',
                'symbol' => 'PIT',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xA57ac35CE91Ee92CaEfAA8dc04140C8e232c2E50',
            ],
            [
                'name' => 'CateCoin',
                'symbol' => 'CATE',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xe4fae3faa8300810c835970b9187c268f55d998f',
            ],
            [
                'name' => 'Hamster',
                'symbol' => 'HAM',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x679d5b2d94f454c950d683d159b87aa8eae37c9e',
            ],
            [
                'name' => 'Floki',
                'symbol' => 'FLOKI',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x2b3f34e9d4b127797ce6244ea341a83733ddd6e4',
            ],
            [
                'name' => 'Binance USDT',
                'symbol' => 'BUSD',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xe9e7cea3dedca5984780bafc599bd69add087d56',
            ],
            [
                'name' => 'Ship',
                'symbol' => 'SHIP',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xc0a696bbb66352e5b88624f1d1b8909c34dc4e4a',
            ],
            [
                'name' => 'Shiba Floki',
                'symbol' => 'FLOKI',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x330540a9d998442dcbc396165d3ddc5052077bb1',
            ],
            [
                'name' => 'Binance Doge Coin',
                'symbol' => 'DOGE',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xba2ae424d960c26247dd6c32edc70b295c744c43',
            ],
            [
                'name' => 'Swipe',
                'symbol' => 'SXP',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x47bead2563dcbf3bf2c9407fea4dc236faba485a',
            ],
            [
                'name' => 'Baby Doge Coin',
                'symbol' => 'BABYDOGE',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xc748673057861a797275CD8A068AbB95A902e8de',
            ],
            [
                'name' => 'Fantom',
                'symbol' => 'FTM',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0xad29abb318791d579433d831ed122afeaf29dcfe',
            ],
            [
                'name' => 'Trust Wallet',
                'symbol' => 'TWT',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x4b0f1812e5df2a09796481ff14017e6005508003',
            ],
            [
                'name' => 'Zigcoin',
                'symbol' => 'ZIG',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['BSC']->first()->id,
                'contract' => '0x8C907e0a72C3d55627E853f4ec6a96b0C8771145',
            ],


            // ETH \//
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
                'name' => 'chiliZ',
                'symbol' => 'CHZ',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x3506424f91fd33084466f402d5d97f05f8e3b4af',
            ],
            [
                'name' => 'Graph Token',
                'symbol' => 'GRT',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0xc944e90c64b2c07662a292be6244bdf05cda44a7',
            ],
            [
                'name' => 'Shiba Inu',
                'symbol' => 'SHIB',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x95ad61b0a150d79219dcf64e1e6cc01f0b64c4ce',
            ],
            [
                'name' => 'Enjin Coin',
                'symbol' => 'ENJ',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0xf629cbd94d3791c9250152bd8dfbdf380e2a3b9c',
            ],
            [
                'name' => 'Holo',
                'symbol' => 'HOT',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x6c6ee5e31d828de241282b9606c8e98ea48526e2',
            ],
            [
                'name' => 'IoTeX',
                'symbol' => 'IOTX',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x6fb3e0a217407efff7ca062d46c26e5d60a14d69',
            ],
            [
                'name' => 'Dogelon Mars',
                'symbol' => 'ELON',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x761d38e5ddf6ccf6cf7c55759d5210750b5d60f3',
            ],
            [
                'name' => 'The Sandbox',
                'symbol' => 'SAND',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x3845badAde8e6dFF049820680d1F14bD3903a5d0',
            ],
            [
                'name' => 'Radio Caca',
                'symbol' => 'RACA',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0x12BB890508c125661E03b09EC06E404bc9289040',
            ],
            [
                'name' => 'CEEK VR',
                'symbol' => 'CEEK',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0xb056c38f6b7dc4064367403e26424cd2c60655e1',
            ],
            [
                'name' => 'MyNeighborAlice',
                'symbol' => 'ALICE',
                'commission_in' => 0.25,
                'commission_out' => 0.25,
                'commission_type' => 'percent',
                'status' => 'normal',
                'networks_id' => $network['ETH']->first()->id,
                'contract' => '0xAC51066d7bEC65Dc4589368da368b212745d63E8',
            ],
        ];

        foreach ($alterCoins as $alterCoin) {
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
