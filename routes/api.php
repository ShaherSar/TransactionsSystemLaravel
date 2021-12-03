<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return auth()->user();
});

Route::post('/login',['\App\Http\Controllers\AuthController','login']);
Route::post('/register',['\App\Http\Controllers\AuthController','register']);

Route::middleware('auth:sanctum')->group(function (){

    Route::get('/payment-methods',['\App\Http\Controllers\PaymentMethodController','index']);
    Route::post('/payment-methods/store',['\App\Http\Controllers\PaymentMethodController','store']);
    Route::get('/payment-methods/{id}',['\App\Http\Controllers\PaymentMethodController','show']);

    Route::get('/transactions',['\App\Http\Controllers\TransactionController','index']);
    Route::post('/transactions/store',['\App\Http\Controllers\TransactionController','store']);
    Route::post('/transactions/update',['\App\Http\Controllers\TransactionController','update']);

    Route::get('/users',['\App\Http\Controllers\UserController','index']);
});
