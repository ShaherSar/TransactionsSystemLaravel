<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletsTableSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('wallets')->insert([
            'balance' => 0,
            'user_id'=>1,
            'created_at' => now(),
            'updated_at'=> now(),
        ]);
    }
}
