<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller{

    public function index(){
        $paymentMethods = PaymentMethod::query()->with('currencies')->get();
        return response()->json($paymentMethods);
    }

    public function store(Request $request){
        $postFields = $request->all();

        $validator = Validator::make($postFields, [
            'name' => 'required|unique:payment_methods|max:255',
            'image' => 'required|max:255',
            'minimum_deposit'=>'required|integer|min:1',
            'minimum_withdrawal'=>'required|integer|min:1',
            'maximum_deposit'=>'required|integer|min:1',
            'maximum_withdrawal'=>'required|integer|min:1',
            'allowed_currencies'=>'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $paymentMethod = new PaymentMethod();
        $paymentMethod->fill($postFields);
        $paymentMethod->save();

        foreach($postFields['allowed_currencies'] as $currency_id){
            $currency = Currency::query()->find($currency_id);
            if($currency != null){
                $paymentMethod->currencies()->attach($currency_id);
            }
        }

        if($paymentMethod->currencies()->count() == 0){
            $paymentMethod->delete();
            return response()->json([
               'errors'=>[
                   'Allowed Currencies ID are incorrect.'
               ]
            ]);
        }

        $paymentMethod = PaymentMethod::with('currencies')->find($paymentMethod->id);
        return response()->json($paymentMethod);
    }

    public function show($id){
        $paymentMethod = PaymentMethod::query()->with('currencies')->find($id);
        return response()->json($paymentMethod);
    }
}
