<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('currency_id');

            $table->enum('type',config('enum.transactions.type'));
            $table->enum('status',config('enum.transactions.status'))->default('Pending');
            $table->double('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('transactions');
    }
}
