<?php

namespace Database\Seeders;

use App\Jobs\WalletCreate;
use Illuminate\Database\Seeder;

class User extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = \App\Models\User::create([
                'username' => "test" . $i,
                'name' => 'Test' . $i,
                'surname' => "--",
                'birthday' => "1998-03-01",
                'nationality' => 218,
                'tck_no' => rand(11111111111, 9999999999),
                'pasaport_no' => null,
                'password' => pssMngr("123"),
                'status' => 2,
            ]);
            WalletCreate::dispatch($user->makeVisible(['id'])->toArray(), 0);
            \App\Models\UserContact::create([
                'users_id' => $user->id,
                'type' => 'email',
                'value' => $i . "@gmail.com",
            ]);
            \App\Models\UserContact::create([
                'users_id' => $user->id,
                'type' => 'telephone',
                'value' => rand(11111111111, 9999999999),
                'nationality' => 218
            ]);
        }
    }
}
