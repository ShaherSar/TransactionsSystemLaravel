<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionController extends Controller{

    public function chart(Request $request){
        $response = array(
            'labels'=>[],
            'data'=>[]
        );
            DB::table('transactions')
            ->selectRaw("count(*) as count,DATE_FORMAT(created_at,'%Y-%m-%d') as date")
            ->groupByRaw("DATE_FORMAT(created_at,'%Y-%m-%d')")
            ->get()->map(function ($row) use (&$response){
                $response['labels'][] = $row->date;
                $response['data'][] = $row->count;
            })
            ->toArray();
        return response()->json($response);
    }

    public function index(Request $request){
        $transactions = Transaction::with(['wallet.user','currency'])->get();
        return response()->json($transactions);
    }

    public function store(Request $request){
        $postFields = $request->all();

        $paymentMethod = PaymentMethod::query()->with('currencies')->find($postFields['payment_method_id']);
        if($paymentMethod == null){
            return [
                'errors'=>['Payment Method Id Not Found']
            ];
        }
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
            'amount'=>'required|numeric|min:.'.$minimum_amount.'|max:'.$maximum_amount,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $transcation = new Transaction();
        $transcation->fill($postFields);
        $transcation->wallet_id = auth()->user()->wallet->id;
        $transcation->save();

        $transcation = Transaction::query()->with([
            'wallet',
            'currency'
        ])->find($transcation->id);

        return response()->json($transcation);
    }

    public function update(Request $request){
        $postFields = $request->all();

        $validator = Validator::make($postFields,[
           'id'=>'required',
           'status'=>[
               'required',
               'status'=>Rule::in(config('enum.transactions.status'))
           ]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $transaction = Transaction::query()->find($postFields['id']);

        if($transaction == null){
            return [
                'errors'=>[
                    'Transaction ID not Found'
                ]
            ];
        }

        if($transaction->status == 'Approved'){
            return response()->json($transaction);
        }

        if($postFields['status'] == 'Approved'){
            if($transaction->type == 'Deposit'){
                $transaction->wallet->balance = $transaction->wallet->balance + ($transaction->amount * $transaction->currency->conversion_rate);
            }else{
                //Withdraw
                if( ($transaction->amount * $transaction->currency->conversion_rate) <= $transaction->wallet->balance){
                    $transaction->wallet->balance = $transaction->wallet->balance - ($transaction->amount * $transaction->currency->conversion_rate);
                }else{
                    return response()->json([
                        'errors'=>'Transaction amount is higher than wallet amount'
                    ]);
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
