<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller{
    public function index(){
        return response()->json(PaymentMethod::all());
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
            'currency'=>[
                'required',
                Rule::in(config('enum.currencies')),
            ]
        ]);

        if ($validator->fails()) {
            //return redirect('post/create')->withErrors($validator)->withInput();
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $paymentMethod = new PaymentMethod();
        $paymentMethod->fill($postFields);
        $paymentMethod->save();

        return response()->json($paymentMethod);
    }

    public function show(PaymentMethod $id){
        return response()->json($id);
    }
}
