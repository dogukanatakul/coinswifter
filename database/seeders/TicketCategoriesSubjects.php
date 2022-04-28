<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TicketCategoriesSubjects extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\TicketCategory::create([
           'name' => 'Transfer'
        ]);
        \App\Models\TicketSubject::create([
           'name' => 'Subject_transfer'
        ]);
    }
}
