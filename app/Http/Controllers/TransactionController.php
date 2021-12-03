<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionController extends Controller{

    public function index(Request $request){
        $transactions = auth()->user()->wallet->transactions;
        return response()->json($transactions);
    }

    public function store(Request $request){
        $postFields = $request->all();

        $paymentMethod = PaymentMethod::query()->with('currencies')->find($postFields['payment_method_id']);

        $currencies = $paymentMethod->currencies->map(function ($value){
            return $value->pivot->currency_id;
        })->toArray();

        $minimum_amount = ($postFields['type'] == 'Deposit') ? $paymentMethod->minimum_deposit:$paymentMethod->minimum_withdrawal;
        $maximum_amount = ($postFields['type'] == 'Deposit') ? $paymentMethod->maximum_deposit:$paymentMethod->maximum_withdrawal;

        $validator = Validator::make($postFields, [
            'payment_method_id' => 'required|max:255',
            'currency_id'=>[
                'required',
                Rule::in($currencies)
            ],
            'type'=>[
                'required',
                Rule::in(config('enum.transactions.type')),
            ],
            'amount'=>'required|integer|min:.'.$minimum_amount.'|max:'.$maximum_amount,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $transcation = new Transaction();
        $transcation->fill($postFields);
        $transcation->wallet_id = auth()->user()->wallet->id;
        $transcation->save();

        return response()->json($transcation);
    }

    public function update(Request $request){
        $postFields = $request->all();
        $transaction = Transaction::query()->find($postFields['id']);

        if($transaction->status == 'Approved'){
            return response()->json($transaction);
        }

        if($postFields['status'] == 'Approved'){
            if($transaction->type == 'Deposit'){
                $transaction->wallet->balance = $transaction->wallet->balance + $transaction->amount;
            }else{
                //Withdraw
                if($transaction->amount <= $transaction->wallet->balance){
                    $transaction->wallet->balance = $transaction->wallet->balance - $transaction->amount;
                }else{
                    return response()->json(['errors'=>'Transaction amount is higher than wallet amount']);
                }
            }
            $transaction->status = 'Approved';
            $transaction->save();
            $transaction->wallet->save();
            return response()->json($transaction);
        }elseif($postFields['status'] == 'Rejected'){
            $transaction->status = $postFields['status'];
            $transaction->save();
            return response()->json($transaction);
        }
    }
}
