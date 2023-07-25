<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AccessMiddleware {
  /**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @return mixed
 */
  public function handle($request, Closure $next)
  {
    // if(!Auth::user()->hashRole('Admin')){
    //   // Abortar
    //   abort(403);
    // }
    return $next($request);
  }
}

?>