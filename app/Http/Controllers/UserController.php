<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class UserController extends Controller{

    public function index(){
        return User::query()->with('wallet')->get();
    }

    public function update(Request $request){
        $postFields = $request->all();

        $validator = Validator::make($postFields,[
           'id'=>'required',
            'status'=>[
                'required',
                Rule::in(config('enum.users.status'))
            ]
        ]);

        if ($validator->fails()){
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $user = User::query()->find($postFields['id']);

        if($user->isNotEmpty()){
            
            $user->update([
                'status'=>$postFields['status']
            ]);

            return response()->json($user);
        }

        return response()->json([
            'errors'=>['User ID Not Found']
        ]);
    }
}
