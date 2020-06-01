<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(empty(Auth::user()->status)){
        abort(403,"คุณไม่มีสิทธิ์เข้าหน้านี้");
        return route('login');
        }else if(Auth::user()->status == 3){
          return $next($request);
        }else{
          abort(403,"คุณไม่มีสิทธิ์เข้าหน้านี้");
          return route('login');
      }
    }
}
