<?php

namespace App\Http\Controllers;

use App\Services\Implementation\AuthService;
use App\Validator\AuthValidator;
use Illuminate\Http\Request;
use App\Services\Implementation\IpAllowedService;

class AuthController extends Controller
{

    private $request;
    private $authService;
    private $authValidator;
    private $ipService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, AuthService $authService, AuthValidator $authValidator, IpAllowedService $ipService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout', 'register']]);
        $this->request = $request;
        $this->authService = $authService;
        $this->authValidator = $authValidator;
        $this->ipService = $ipService;
    }


    public function login()
    {
        try {

            $ip = $this->request->ip();

            $result = $this->ipService->getFilterByIP($ip);


            if (count($result) == 0) {
                return $this->responseError(['message' => 'No cuenta con acceso', 'error' => ''], 403);
            }


            $validator = $this->authValidator->validate();
            if ($validator->fails()) {
                $response = $this->responseError($validator->errors(), 422);
            } else {
                $credentials = $this->request->only('nombre_usuario', 'clave');
                $result = $this->authService->login($credentials);
                $response = $this->response($result);
            }

            return $response;
        } catch (\Exception $e) {
            return $this->responseError(['message' => 'Error al iniciar sesión', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout($id)
    {
        try {
            $result = $this->authService->logout($id);
            return $this->response($result);
        } catch (\Exception $e) {
            return $this->responseError(['message' => 'Error al cerrar sesión', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function logout()
    // {
    //     auth()->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
