<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Officer
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
    }else if(Auth::user()->status == 3 || Auth::user()->status == 2){

          return $next($request);
      }else{
          abort(403,"คุณไม่มีสิทธิ์เข้าหน้านี้");
          return route('login');
      }
    }
}
