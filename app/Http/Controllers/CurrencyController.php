<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class CurrencyController extends Controller{
    public function index(){
        $currencies = Currency::query()->get();
        return response()->json($currencies);
    }
}
