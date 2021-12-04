<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveRole{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        if(auth()->user()->status == 'Active'){
            return $next($request);
        }
        return response()->json([
            'errors'=>'You Are Not Active User'
        ]);
    }
}
