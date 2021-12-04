<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        //from currency to jod
        DB::table('currencies')->insert([
            ['name'=>'JOD','conversion_rate'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'USD','conversion_rate'=>0.71,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'EUR','conversion_rate'=>0.80,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'AUD','conversion_rate'=>0.50,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'TRY','conversion_rate'=>0.052,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
