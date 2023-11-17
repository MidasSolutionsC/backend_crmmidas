<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\AddressService;
use App\Validator\AddressValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller{

  private $request;
  private $addressService;
  private $addressValidator;

  public function __construct(Request $request, AddressService $addressService, AddressValidator $addressValidator)
  {
    $this->request = $request;
    $this->addressService = $addressService;
    $this->addressValidator = $addressValidator;
  }

  public function listAll(){
    try{
      $result = $this->addressService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las direcciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByPerson(int $personId){
    try{
      $result = $this->addressService->getFilterByPerson($personId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las direcciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterByCompany(int $companyId){
    try{
      $result = $this->addressService->getFilterByCompany($companyId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar las direcciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->addressService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos de la dirección', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->addressValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->addressService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear usuario de la dirección', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try {
      // Iniciar una transacción
      DB::beginTransaction();
      $resultFull = [];

      $personId = $this->request->input('personas_id');
      $companyId = $this->request->input('empresas_id');

      $dataArray = $this->request->input('data_array');
      foreach ($dataArray as $row) {
        $row['user_create_id'] = $this->request->input('user_auth_id');
        if (!empty($personId)) {
          unset($row['empresas_id']);
          $row['personas_id'] = $personId;
        }

        if (!empty($companyId)) {
          unset($row['personas_id']);
          $row['empresas_id'] = $companyId;
        }

        $this->addressValidator->setRequest($row);
        $validator = $this->addressValidator->validate();

        if ($validator->fails()) {
          $response = $this->responseError($validator->errors(), 422);
        } else {
          $result = $this->addressService->create($row);
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
      return $this->responseError(['message' => 'Error al registrar las direcciones', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch (\Exception $e) {
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear las direcciones', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->addressValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->addressService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la dirección', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos de la dirección', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->addressService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar la dirección', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->addressService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar la dirección', 'error' => $e->getMessage()], 500);
    }
    
  }
}