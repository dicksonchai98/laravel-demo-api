<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     if($request->input('api_token')!=='' ){
    //         return response(['message'=>'login success!'])
    //     }
    //     return $next($request);
    // }
        public function handle(Request $request, Closure $next)
    {
        $api_token = request()->header('api_token');
        $auth_user = User::where('api_token',$api_token);
        if(!$auth_user->exists()){
            return response(['message'=>'Authentication error']);
        }
        $request->attributes->set('auth_user',$auth_user);
        return $next($request);
    }
}