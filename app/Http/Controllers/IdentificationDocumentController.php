<?php
namespace App\Http\Controllers;

use App\Services\Implementation\IdentificationDocumentService;
use App\Validator\IdentificationDocumentValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IdentificationDocumentController extends Controller{

  private $request;
  private $identificationDocumentService;
  private $identificationDocumentValidator;

  public function __construct(Request $request, IdentificationDocumentService $identificationDocumentService, IdentificationDocumentValidator $identificationDocumentValidator)
  {
    $this->request = $request;
    $this->identificationDocumentService = $identificationDocumentService;
    $this->identificationDocumentValidator = $identificationDocumentValidator;
  }

  public function listAll()
  {
    try {
      $result = $this->identificationDocumentService->getAll();
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
      $result = $this->identificationDocumentService->getFilterByCompany($companyId);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos del documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByPerson($personId)
  {
    try {
      $result = $this->identificationDocumentService->getFilterByPerson($personId);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos del documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id)
  {
    try {
      $result = $this->identificationDocumentService->getById($id);
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
      $validator = $this->identificationDocumentValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->identificationDocumentService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al crear el documento', 'error' => $e->getMessage()], 500);
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

      $documentos = $this->request->input('data_array');
      foreach ($documentos as $document) {
        $document['user_create_id'] = $this->request->input('user_auth_id');
        if (!empty($personId)) {
          unset($document['empresas_id']);
          $document['personas_id'] = $personId;
        }

        if (!empty($companyId)) {
          unset($document['personas_id']);
          $document['empresas_id'] = $companyId;
        }

        $this->identificationDocumentValidator->setRequest($document);
        $validator = $this->identificationDocumentValidator->validate();

        if ($validator->fails()) {
          $response = $this->responseError($validator->errors(), 422);
        } else {
          $result = $this->identificationDocumentService->create($document);
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
      return $this->responseError(['message' => 'Error al registrar los documentos', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch (\Exception $e) {
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear los documentos', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id)
  {
    try {
      $validator = $this->identificationDocumentValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->identificationDocumentService->update($this->request->all(), $id);
        if ($result != null) {
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del documento', 'error' => $result]);
        }
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al actualizar los datos del documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id)
  {
    try {
      $result = $this->identificationDocumentService->delete($id);
      if ($result) {
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al eliminar el documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id)
  {
    try {
      $result = $this->identificationDocumentService->restore($id);
      if ($result) {
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al restaurar el documento', 'error' => $e->getMessage()], 500);
    }
  }
}