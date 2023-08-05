<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
  // public function handle($request, Closure $next)
  // {
  //   // Agregar los encabezados CORS necesarios
  //   return $next($request)
  //     ->header('Access-Control-Allow-Origin', '*')
  //     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
  //     ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Origin')
  //     ->header('Access-Control-Allow-Credentials', 'false');
  //     // ->header('Access-Control-Max-Age', '1'); // Ejemplo de max_age;
  // }

  public function handle($request, Closure $next)
  {
    $headers = [
      'Access-Control-Allow-Origin' => '*',
      'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
      'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization'
    ];

    if ($request->getMethod() == "OPTIONS") {
      return response()->json('OK', 200, $headers);
    }

    $response = $next($request);
    foreach ($headers as $key => $value) {
      $response->header($key, $value);
    }

    return $response;
  }
}
