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
        $bscEthWallets = [
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
                    'wallet' => $bscEthWallets[$i]['wallet'],
                    'password' => $bscEthWallets[$i]['password']
                ]);
            }
            $i++;
        }


        $tronWallets = [
            [
                'wallet' => 'TM1YArX51J63sBPaqjtzD4kd5Pj2UoqMxw',
                'wallet_hex' => '41791a37889a78cae08dfd89c3a6730a806b24032b',
                'password' => 'af4cb04735ce593167c2c25b38cc09689950314f6b5fcefdbf088655c1104f54'
            ],
            [
                'wallet' => 'THpu69T3iyZN84ews942wKWYEyiuQh9bBN',
                'wallet_hex' => '41562eb9e53556a10630db079f96f9a0c0f35b7c98',
                'password' => '6d4d1022880be850aa6a27c87070c6412aaf549ca38f0a509f1f9e942f6805f5'
            ],
            [
                'wallet' => 'TEsZBknuN21tAEny4fDRtQm4E67gci8QQ2',
                'wallet_hex' => '4135c6fb756ab61f67c1d157df700d6080c8247fe5',
                'password' => '254b298cbe3eacb0ae0bafb7128f3c760848f16e9922a1604dc96193a1effea7'
            ],
            [
                'wallet' => 'TXe8Piomsdq2AyCRsaWHJY8hbFJK8wy3LF',
                'wallet_hex' => '41edb7042f66a6a5e5b4a97d8325b229e2d0baf882',
                'password' => 'bbc37350f0b1b576372ab5360d77e12bc11c9d6b36cbad8d74d7a483d4d032f9'
            ],
            [
                'wallet' => 'TPxkSGA64S7SgDpU48RtnehHRRQn7hxXso',
                'wallet_hex' => '41997b95d25b546d7a09bd53588c25949780e80794',
                'password' => '58b4bbba22fb80474a188e428dcd64655c1081cdcb2340060f35c3fe24d843bc'
            ],
            [
                'wallet' => 'TWjTr2Cbn2baXjgTRmFnvziJwmQ4iXpSyJ',
                'wallet_hex' => '41e3c135943491fbf365e1f318622a7786cb37f7b2',
                'password' => 'd3d9fa5b171cb4db565bbfd2a04c8243e0b377cbc939851fdd9a6c63f9732dc2'
            ],
            [
                'wallet' => 'TK7rpm3ehc9csiLP7LgHrFLSejVpBqHWJa',
                'wallet_hex' => '41645c0846952535fcc52ae480b985918fcc94b701',
                'password' => 'eecc0d1a293c33696fe2d8c1916c903589eecff94467cd1adf85c7d432e5851f'
            ],
            [
                'wallet' => 'TBubfeLTKNMNfFc8JfL7oEzaTaQQung7bY',
                'wallet_hex' => '41154181facc7b874b5c0ff4c6172ba07c182a3f17',
                'password' => '55820c02162ffe4866d13e894feff0bcb8f9315d60c82685e8513189b94f19dd'
            ],
            [
                'wallet' => 'TZ6ctkJ1RFUUY2UVBmHdSikfbFNtAW4r9N',
                'wallet_hex' => '41fdb1bf841985840fd5044d95cd658856df78fd10',
                'password' => '0261676416a574a4c38eebc493a461b32609161c836b7b85778038429c571859'
            ],
            [
                'wallet' => 'TEvyQnSxTSoBNDD3hr62Zn1DrjmXMxRkxW',
                'wallet_hex' => '41366c73ac5b7b0c823d543728ee0b0dc90c9f4b97',
                'password' => 'c8c8d446c2a00ba7403d01dd082c8844067e4b03f4abfb4e42725f1f746603fb'
            ],
        ];
        $i = 0;
        foreach ($users as $user) {
            UserWallet::create([
                'users_id' => $user->id,
                'networks_id' => 3,
                'wallet' => $tronWallets[$i]['wallet'],
                'wallet_hex' => $tronWallets[$i]['wallet_hex'],
                'password' => $tronWallets[$i]['password']
            ]);
            $i++;
        }
    }
}
