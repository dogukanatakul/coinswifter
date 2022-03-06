<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Network extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $networks = [
            [
                'name' => 'Binance Smart Chain',
                'short_name' => 'BSC',
                'connection' => '',
                'fee' => 0.002,
            ],
            [
                'name' => 'Ethereum',
                'short_name' => 'ETH',
                'connection' => '',
                'fee' => 0.004,
            ],
            [
                'name' => 'Dexchain',
                'short_name' => 'DXC',
                'connection' => '',
                'fee' => 0.003,
            ],
            [
                'name' => 'Source',
                'short_name' => 'SOURCE',
                'connection' => '',
            ],
        ];

        foreach ($networks as $network) {
            \App\Models\Network::create($network);
        }
    }
}
