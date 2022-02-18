<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $path = database_path('seeders' . DIRECTORY_SEPARATOR . 'countries.sql');
        \Illuminate\Support\Facades\DB::unprepared(file_get_contents($path));
        printf("\n\rÜlkeler Eklendi\n\r");

        $path = database_path('seeders' . DIRECTORY_SEPARATOR . 'provinces.sql');
        \Illuminate\Support\Facades\DB::unprepared(file_get_contents($path));
        printf("\n\rİller Eklendi\n\r");

        $path = database_path('seeders' . DIRECTORY_SEPARATOR . 'district.sql');
        \Illuminate\Support\Facades\DB::unprepared(file_get_contents($path));
        printf("\n\rİlçeler Eklendi\n\r");

        $this->call(Network::class);
        $this->call(Coin::class);
//        $this->call(User::class);
        $this->call(Bank::class);
        $this->call(ContractedBank::class);

    }
}
