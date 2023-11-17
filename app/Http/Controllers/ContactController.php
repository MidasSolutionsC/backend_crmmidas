<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\ContactService;
use App\Validator\ContactValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{

  private $request;
  private $contactService;
  private $contactValidator;

  public function __construct(Request $request, ContactService $contactService, ContactValidator $contactValidator)
  {
    $this->request = $request;
    $this->contactService = $contactService;
    $this->contactValidator = $contactValidator;
  }

  public function listAll()
  {
    try {
      $result = $this->contactService->getAll();
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al listar los contactos', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByCompany($companyId)
  {
    try {
      $result = $this->contactService->getFilterByCompany($companyId);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos del contacto', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByPerson($personId)
  {
    try {
      $result = $this->contactService->getFilterByPerson($personId);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos del contacto', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id)
  {
    try {
      $result = $this->contactService->getById($id);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos del contacto', 'error' => $e->getMessage()], 500);
    }
  }

  public function create()
  {
    try {
      $validator = $this->contactValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->contactService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al crear el contacto', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete()
  {
    try {
      // Iniciar una transacción
      DB::beginTransaction();
      $resultFull = [];

      $personId = $this->request->input('personas_id');
      $companyId = $this->request->input('empresas_id');

      $contactos = $this->request->input('data_array');
      foreach ($contactos as $contact) {
        $contact['user_create_id'] = $this->request->input('user_auth_id');
        if (!empty($personId)) {
          unset($contact['empresas_id']);
          $contact['personas_id'] = $personId;
        }

        if (!empty($companyId)) {
          unset($contact['personas_id']);
          $contact['empresas_id'] = $companyId;
        }

        $this->contactValidator->setRequest($contact);
        $validator = $this->contactValidator->validate();

        if ($validator->fails()) {
          $response = $this->responseError($validator->errors(), 422);
        } else {
          $result = $this->contactService->create($contact);
          $resultFull[] = $result;
        }
      }

      $response = $this->responseCreated($resultFull);

      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      // Maneja la excepción, por ejemplo, muestra un mensaje de error
      DB::rollBack();
      return $this->responseError(['message' => 'Error al registrar los contactos', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch (\Exception $e) {
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear los contactos', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id)
  {
    try {
      $validator = $this->contactValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->contactService->update($this->request->all(), $id);
        if ($result != null) {
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del contacto', 'error' => $result]);
        }
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al actualizar los datos del contacto', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id)
  {
    try {
      $result = $this->contactService->delete($id);
      if ($result) {
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al eliminar el contacto', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id)
  {
    try {
      $result = $this->contactService->restore($id);
      if ($result) {
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al restaurar el contacto', 'error' => $e->getMessage()], 500);
    }
  }
}
