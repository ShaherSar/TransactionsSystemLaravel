<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller{

    public function show(Request $request){
        $data = User::query()->with('wallet.transactions')->find(auth()->user()->id);
        return response()->json($data);
    }

    public function login(LoginRequest $request){
        $postFields = $request->all();

        $user = User::query()->where('email', $postFields['email'])->first();

        if (! $user || ! Hash::check($postFields['password'], $user->password)) {
            return response()->json([
                'errors'=>[
                    'The provided credentials are incorrect.'
                ]
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function register(RegisterRequest $request){
        $postFields = $request->all();

        $postFields['password'] = Hash::make($postFields['password']);

        $user = User::create($postFields);
        $user->wallet()->create();

        return response()->json([
            'success'=>1,
            'msg'=>'registered successfully'
        ]);
    }
}
