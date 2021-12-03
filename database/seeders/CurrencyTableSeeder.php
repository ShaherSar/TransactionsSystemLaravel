<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        //from currency to jod
        DB::table('currencies')->insert([
            ['name'=>'USD','conversion_rate'=>0.71],
            ['name'=>'EUR','conversion_rate'=>0.80],
            ['name'=>'AUD','conversion_rate'=>0.50],
            ['name'=>'TRY','conversion_rate'=>0.052],
        ]);
    }
}
