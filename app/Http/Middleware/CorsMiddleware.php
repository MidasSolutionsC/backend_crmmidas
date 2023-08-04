<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
  public function handle($request, Closure $next)
  {
    // Agregar los encabezados CORS necesarios
    return $next($request)
      ->header('Access-Control-Allow-Origin', '*')
      ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
      ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Origin')
      ->header('Access-Control-Allow-Credentials', 'false')
      ->header('Access-Control-Max-Age', '3600'); // Ejemplo de max_age;
  }
}
