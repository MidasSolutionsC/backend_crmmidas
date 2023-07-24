<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    // RESPUESTA CORRECTA
    public function response($data = [], int $codeStatus = 200){
        return response()->json([
            'code' => $codeStatus,
            'status' => 'OK',
            'message' => 'Operación exitosa.', 
            'data' => $data
        ], $codeStatus);
    }

    // RESPUESTA DE REGISTRO CORRECTO
    public function responseCreated($data = [], int $codeStatus = 201){
        return response()->json([
            'code' => $codeStatus,
            'status' => 'Created',
            'message' => 'El recurso se ha creado exitosamente.',
            'data' => $data,
        ], $codeStatus);
    }

    // RESPUESTA DE ERROR
    public function responseError($errors = [], int $codeStatus = 400){
        return response()->json([
            'code' => $codeStatus,
            'status' => 'Error',
            'message' => 'Ocurrió un error inesperado!', 
            'errors' => $errors
        ], $codeStatus);
    }

    // RESPUESTA DE MODIFICACIÓN 
    public function responseUpdate($data = [], int $codeStatus = 200){
        return response()->json([
            'code' => $codeStatus,
            'status' => 'OK',
            'message' => 'El recurso se ha modificado exitosamente.',
            'data' => $data,
        ], $codeStatus);
    }

    // RESPUESTA DE ELIMINACIÓN 
    public function responseDelete($data = [], int $codeStatus = 200){
        return response()->json([
            'code' => $codeStatus,
            'status' => 'OK',
            'message' => 'El recurso se ha eliminado exitosamente.',
            'data' => $data,
        ], $codeStatus);
    }

    // RESPUESTA DE RESTAURACIÓN 
    public function responseRestore($data = [], int $codeStatus = 200){
        return response()->json([
            'code' => $codeStatus,
            'status' => 'OK',
            'message' => 'El recurso se ha restaurado exitosamente.',
            'data' => $data
        ], $codeStatus);
    }
}
