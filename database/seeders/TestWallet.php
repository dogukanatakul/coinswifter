<?php

namespace Database\Seeders;

use App\Models\UserWallet;
use Illuminate\Database\Seeder;

class TestWallet extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testWallets = [
            [
                "wallet" => "0x3d7B339CddddD67a189E4d16b51138B70cB6807F",
                "password" => "0xee06b6340b5e66ab8bcb597a19cab724722c90d95517282fce7b89a9a6400e6f"
            ],
            [
                "wallet" => "0x91eFF5d90f303a1E511Bdd8Df62b9814E4c6B877",
                "password" => "0x4d29ef8ad3d197781762685d8d4a0e49f1e9b53a8e660d9a3b17a58b7bdadf18"
            ],
            [
                "wallet" => "0xac66E8D0580680716bBC250c401b909Efa6d4C6a",
                "password" => "0x3a10d0b4c50c026396abe211d4943913c177ae1623cfccbc20f4c4ea6600fc7a"
            ],
            [
                "wallet" => "0x44ed11c9aad3fD4aDc4Fa174333AB7Cdef24BB52",
                "password" => "0x9a00956bc26810ec2f3aa9ba3a1c3a7b4680fafcd7ff8148d2ce073dab2b3fe9"
            ],
            [
                "wallet" => "0x6037F4B4968A54374f7728d1201CAAaD599343a6",
                "password" => "0x14698541749ccdee909a96d0197fdc2c9e4d3c0dc18e560439bf765d2598bb8d"
            ],
            [
                "wallet" => "0x93C3f27DCb365D4e650d943f81CBD676B756520b",
                "password" => "0xf3705eb68f112f3e80492c086fa3bb984b5c94c6e850ec7f2de3475d8620de58"
            ],
            [
                "wallet" => "0x735a2CfE50101380803DE571783FB674cD1c9509",
                "password" => "0x41fb0548fcfe0aa62dc586c75d8ce478894a5ba0c7f1e18ba0b668827b0b8720"
            ],
            [
                "wallet" => "0xBFAC9C265C9ae00d0F5ab114e8DE7bf208a2d9F4",
                "password" => "0x4b48a7609485489a1e912aa5ac3159ea884094f8a689dca1fc72d9dac612b5fa"
            ],
            [
                "wallet" => "0xC6c80b2b7fcb979A0f84EEc65b5Ba09a9E929661",
                "password" => "0xf3d1fb260ebdb64e3d7c554e74d3c6c749b0243b9833ff32edd3821ce4fc035e"
            ],
            [
                "wallet" => "0x853472183B45B3608cF4dAd920c5A92bD7f496a3",
                "password" => "0x79bd7e4174f39b86d88c67d061e8e28bee8e6cbd1035abdda50ea3780b1011b4"
            ]
        ];

        $users = \App\Models\User::get();

        $i = 0;
        foreach ($users as $user) {
            for ($c = 1; $c <= 2; $c++) {
                UserWallet::create([
                    'users_id' => $user->id,
                    'networks_id' => $c,
                    'wallet' => $testWallets[$i]['wallet'],
                    'password' => $testWallets[$i]['password']
                ]);
            }
            $i++;
        }
    }
}
