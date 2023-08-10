<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class AccessMiddleware {


  /**
   * The authentication guard factory instance.
   *
   * @var \Illuminate\Contracts\Auth\Factory
   */
  protected $auth;

  /**
   * Create a new middleware instance.
   *
   * @param  \Illuminate\Contracts\Auth\Factory  $auth
   * @return void
   */
  public function __construct(Auth $auth)
  {
      $this->auth = $auth;
  }

  /**
 * Handle an incoming request.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \Closure  $next
 * @return mixed
 */
  public function handle($request, Closure $next,  $guard = null)
  {
    // if(!Auth::user()->hashRole('Admin')){
    //   // Abortar
    //   abort(403);
    // }
    // if($request->input('clave') != '12345'){
    //   return response('Unauthorized.', 401);
    //   //return redirect('/v1/usuario');
    // }
    if ($this->auth->guard($guard)->guest()) {
      return response()->json([
          'code' => 401,
          'status' => 'Unauthorized',
          'message' => 'No tiene autorización para acceder a este recurso.', 
      ], 401);
      // return response('Unauthorized.', 401);
    }

    return $next($request);
  }
}

?>