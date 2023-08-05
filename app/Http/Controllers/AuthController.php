<?php

namespace App\Http\Controllers;

use App\Services\Implementation\AuthService;
use App\Validator\UserValidator;
use Illuminate\Http\Request;


class AuthController extends Controller {

    private $request;
    private $authService;
    private $userValidator;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthService $authService, UserValidator $userValidator) {
        $this->request = $request;
        $this->authService = $authService;
        $this->userValidator = $userValidator;
    }


    public function login(){
        try{
            $validator = $this->userValidator->validate('auth');
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
