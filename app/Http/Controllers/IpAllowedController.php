<?php

namespace App\Http\Controllers;

use App\Services\Implementation\IpAllowedService;
use Illuminate\Http\Request;
use App\Validator\IpAllowedValidator;

class IpAllowedController extends Controller
{
  private $request;
  private $ipAllowedService;
  private $ipAllowedValidator;

  public function __construct(Request $request, IpAllowedService $ipAllowedService, IpAllowedValidator $ipAllowedValidator)
  {
    $this->request = $request;
    $this->ipAllowedService = $ipAllowedService;
    $this->ipAllowedValidator = $ipAllowedValidator;
  }

  public function listAll()
  {
    try {
      $result = $this->ipAllowedService->getAll();
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al listar las IPs permitidas', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id)
  {
    try {
      $result = $this->ipAllowedService->getById($id);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener las IPs permitidas', 'error' => $e->getMessage()], 500);
    }
  }

  public function create()
  {
    try {
      $validator = $this->ipAllowedValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->ipAllowedService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al registrar la IP', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id)
  {
    try {
      $validator = $this->ipAllowedValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->ipAllowedService->update($this->request->all(), $id);
        if ($result != null) {
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la IP', 'error' => $result]);
        }
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al actualizar los datos de la IP', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id)
  {
    try {
      $result = $this->ipAllowedService->delete($id);
      if ($result) {
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al eliminar la IP', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id)
  {
    try {
      $result = $this->ipAllowedService->restore($id);
      if ($result) {
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al restaurar la IP', 'error' => $e->getMessage()], 500);
    }
  }
}
