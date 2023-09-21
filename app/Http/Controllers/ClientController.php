<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\ClientService;
use App\Services\Implementation\CompanyService;
use App\Services\Implementation\PersonService;
use App\Validator\ClientValidator;
use App\Validator\CompanyValidator;
use App\Validator\PersonValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller{

  private $request;
  private $clientService;
  private $clientValidator;

  private $personService;
  private $personValidator;

  private $companyService;
  private $companyValidator;

  public function __construct(
    Request $request, 
    ClientService $clientService, 
    ClientValidator $clientValidator,
    PersonService $personService,
    PersonValidator $personValidator,
    CompanyService $companyService,
    CompanyValidator $companyValidator,
    ) {
    $this->request = $request;
    $this->clientService = $clientService;
    $this->clientValidator = $clientValidator;
    $this->personService = $personService;
    $this->personValidator = $personValidator;
    $this->companyService = $companyService;
    $this->companyValidator = $companyValidator;
  }

  public function listAll(){
    try{
      $result = $this->clientService->getAll();
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los clientes', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->clientService->getById($id);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function getByPersonId($personId){
    try{
      $result = $this->clientService->getByPersonId($personId);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function getByCompanyId($companyId){
    try{
      $result = $this->clientService->getByCompanyId($companyId);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->update($this->request->all(), $id);
        if(!is_null($result)){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del cliente', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $dataPerson = $this->request->input('datos_persona');
      $dataCompany = $this->request->input('datos_empresa');
      $personaJuridica = $this->request->input('persona_juridica');
      $this->request['persona_juridica'] = boolval($personaJuridica);

      if(boolval($personaJuridica)){
        $requestWithoutAttribute = $this->request->except(['personas_id']);
        $this->clientValidator->setRequest($requestWithoutAttribute);

        if(empty($this->request->input('empresas_id'))){
          // registrar empresa
          $this->companyValidator->setRequest($dataCompany);
          $validatorCompany = $this->companyValidator->validate();
          if($validatorCompany->fails()){
            $response = $this->responseError($validatorCompany->errors(), 422);
          } else {
            // registrar empresa
            $dataCompany->user_create_id = $this->request->input('user_auth_id');
            $result = $this->companyService->create($dataCompany);
            if($result){
              $this->request['empresas_id'] = $result->id;
            }
          }
        }
      } else {
        // PERSONA
        $requestWithoutAttribute = $this->request->except(['empresas_id']);
        $this->clientValidator->setRequest($requestWithoutAttribute);

        if(empty($this->request->input('personas_id'))){
          $this->personValidator->setRequest($dataPerson);
          $validatorPerson = $this->personValidator->validate();
          if($validatorPerson->fails()){
            $response = $this->responseError($validatorPerson->errors(), 422);
          } else {
            // registrar persona
            $dataPerson->user_create_id = $this->request->input('user_auth_id');
            $result = $this->personService->create($dataPerson);
            if($result){
              $this->request['personas_id'] = $result->id;
            }
          }
        }
      }

      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch(\Exception $e){
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function updateComplete($id){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $dataPerson = $this->request->input('datos_persona');
      $dataCompany = $this->request->input('datos_empresa');
      $personaJuridica = $this->request->input('persona_juridica');

      if(boolval($personaJuridica)){
        $requestWithoutAttribute = $this->request->except(['personas_id']);
        $this->clientValidator->setRequest($requestWithoutAttribute, $id);

        if(empty($this->request->input('empresas_id'))){
          // actualizar empresa
        }
      } else {
        $requestWithoutAttribute = $this->request->except(['empresas_id']);
        $this->clientValidator->setRequest($requestWithoutAttribute, $id);
        if(empty($this->request->input('personas_id'))){
          // actualizar persona
        }
      }

      $validator = $this->clientValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->clientService->update($this->request->all(), $id);
        $response = $this->responseUpdate([$result]);
      }
  
      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch(\Exception $e){
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al modificar los datos del cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->clientService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
      
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el cliente', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try {
      $result = $this->clientService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el cliente', 'error' => $e->getMessage()], 500);
    }
  }
}