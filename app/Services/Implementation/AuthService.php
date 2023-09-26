<?php

namespace App\Services\Implementation;

use App\Models\Person;
use App\Models\TypeUser;
use App\Models\User;
use App\Services\Interfaces\IAuth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements IAuth
{

  private $model;

  public function __construct()
  {
    $this->model = new User();
  }

  public function login(array $data)
  {
    $usuario = $this->model->where('nombre_usuario', $data['nombre_usuario'])->first();
    $result = [
      'login' => false,
      'message' => 'Usuario no encontrado'
    ];

    if (!is_null($usuario)) {

      if (Hash::check($data['clave'], $usuario->clave)) {

        $tokenAuth = JWTAuth::fromUser($usuario);
        $expiresAt  = Carbon::now()->addHours(12);

        // $usuario->api_token = $apiToken;
        $usuario->expires_at = $expiresAt;
        $usuario->session_activa = 1;
        $usuario->ultima_conexion = Carbon::now();
        $usuario->save();

        $tipo_usuario = TypeUser::find($usuario->tipo_usuarios_id);

        $usuario['tipo_usuario'] = $tipo_usuario ? strtoupper(trim($tipo_usuario->nombre)) : '';

        $result = [
          'login' => true,
          'message' => 'Bienvenido al sistema',
          'token_auth' => $tokenAuth,
          'usuario' => $usuario,

        ];

        if ($usuario) {
          $person = Person::find($usuario->personas_id);
          $result['persona'] = $person;
        }
      } else {
        $result['message'] = 'Contraseña incorrecta';
      }
    }

    return $result;

    // try{
    //   if(!$token = JWTAuth::attempt($data)){
    //     return ['error' => 'invalid credentials'];
    //   }
    // } catch(JWTException $e){
    //   return ['error' => 'not created token'];
    // }

    // return compact('token');
  }

  public function logout(int $id)
  {
    $usuario = $this->model->find($id);
    $result = [
      'login' => true,
      'message' => 'Usuario no encontrado, no se pudo cerrar la sesión'
    ];

    if (!is_null($usuario)) {
      // auth()->logout();
      $usuario->api_token = NULL;
      $usuario->expires_at = NULL;
      $usuario->session_activa = 0;
      $usuario->ultima_conexion = Carbon::now();
      $usuario->save();

      $result = [
        'login' => false,
        'message' => 'Sesión cerrada, hasta pronto...'
      ];
    }

    return $result;
  }
}
