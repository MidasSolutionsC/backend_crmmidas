<?php

namespace App\Http\Controllers;

use App\Services\Implementation\AuthService;
use Illuminate\Http\Request;
use App\Validator\UsuarioValidator;


class AuthController extends Controller {

    private $request;
    private $authService;
    private $usuarioValidator;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthService $authService, UsuarioValidator $usuarioValidator) {
        $this->request = $request;
        $this->authService = $authService;
        $this->usuarioValidator = $usuarioValidator;
    }


    public function login(){
        try{
            $validator = $this->usuarioValidator->validate('auth');
            if($validator->fails()){
            $response = $this->responseError($validator->errors(), 422);
            } else {
            $result = $this->authService->login($this->request->all());
            $response = $this->response($result);
            }

            return $response;
        } catch(\Exception $e){
            return $this->responseError(['message' => 'Error al iniciar sesión', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout($id){
        try{
            $result = $this->authService->logout($id);
            return $this->response($result);
        } catch(\Exception $e){
            return $this->responseError(['message' => 'Error al cerrar sesión', 'error' => $e->getMessage()], 500);
        }
    }
}
