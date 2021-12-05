<?php
//header('Access-Control-Allow-Origin:  *');
//header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');
//header('Access-Control-Allow-Headers:  Content-Type, X-Auth-Token, Origin, Authorization');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login',['\App\Http\Controllers\AuthController','login']);
Route::post('/register',['\App\Http\Controllers\AuthController','register']);

Route::middleware(['auth:sanctum','active'])->group(function (){
    Route::get('/user',['App\Http\Controllers\AuthController','show']);
    Route::get('/payment-methods',['\App\Http\Controllers\PaymentMethodController','index']);
    Route::get('/payment-methods/{id}',['\App\Http\Controllers\PaymentMethodController','show']);
    Route::post('/transactions/store',['\App\Http\Controllers\TransactionController','store']);
    Route::get('/currencies',['\App\Http\Controllers\CurrencyController','index']);

    Route::middleware(['admin'])->group(function () {
        Route::post('/payment-methods/store',['\App\Http\Controllers\PaymentMethodController','store'])->middleware();

        Route::get('/transactions',['\App\Http\Controllers\TransactionController','index']);
        Route::post('/transactions/update',['\App\Http\Controllers\TransactionController','update']);
        Route::get('/transactions/chart',['\App\Http\Controllers\TransactionController','chart']);

        Route::get('/users',['\App\Http\Controllers\UserController','index']);
        Route::post('/users/update',['\App\Http\Controllers\UserController','update']);
    });
});
