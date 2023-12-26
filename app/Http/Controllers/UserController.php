<?php
namespace App\Http\Controllers;

use App\Models\TypeUser;
use App\Services\Implementation\IdentificationDocumentService;
use App\Services\Implementation\PersonService;
use App\Services\Implementation\TypeUserService;
use Illuminate\Http\Request;
use App\Services\Implementation\UserService;
use App\Validator\IdentificationDocumentValidator;
use App\Validator\PersonValidator;
use App\Validator\UserValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller{

  private $request;
  
  private $personService;
  private $personValidator;
  
  private $identificationService;
  private $identificationValidator;

  private $typeUserService;
  private $userService;
  private $userValidator;

  public function __construct(
    Request $request, 
    TypeUserService $typeUserService,
    UserService $userService, 
    UserValidator $userValidator, 
    PersonService $personService,
    PersonValidator $personValidator,
    IdentificationDocumentService $identificationService,
    IdentificationDocumentValidator $identificationValidator,
  ) {
    $this->request = $request;
    $this->typeUserService = $typeUserService;
    $this->userService = $userService;
    $this->userValidator = $userValidator;
    $this->personService = $personService;
    $this->personValidator = $personValidator;
    $this->identificationService = $identificationService;
    $this->identificationValidator = $identificationValidator;
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->userService->index($data);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->userService->getAll();
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios', 'error' => $e->getMessage()], 500);
    }
  }

  public function getAllServerSide(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->userService->getAllServerSide($data);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los usuarios', 'error' => $e->getMessage()], 500);
    }
  }
 public function get($id){
    try{
      $result = $this->userService->getById($id);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->userValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->userService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){    
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $identificaciones = $this->request->input('identificaciones');
      $resultIdentificaciones = [];

      $validatorPerson = $this->personValidator->validate();
      $validator = $this->userValidator->validateNotPersonId();

      $combinedErrors = [];
      $validationError = false; // Variable de control
        
      if ($validatorPerson->fails()) {
        $combinedErrors['person_errors'] = $validatorPerson->errors();
      }
      
      if ($validator->fails()) {
        $combinedErrors['user_errors'] = $validator->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {
        $resPerson = $this->personService->create($this->request->all());
        if($resPerson){
          $this->request['personas_id'] = $resPerson->id;

          foreach($identificaciones as $identity){
            $identity['personas_id'] = $resPerson->id;
            $identity['user_create_id'] = $this->request->input('user_auth_id');
            $this->identificationValidator->setRequest($identity);
            $validatorIdentity = $this->identificationValidator->validate();

            if ($validatorIdentity->fails()) {
              $combinedErrors['person_errors'] = $validatorIdentity->errors();
              $validationError = true;
              break; // Detener el ciclo en caso de error
            } else {
              $resultIdentificaciones[] = $this->identificationService->create($identity);
            }
          }

        } else {
          $response = $this->responseError(['message' => 'Error al obtener el registro como persona'], 422);
        }
        
        $typeUser = $this->typeUserService->getByName('invitado');
        if(!is_null($typeUser)){
          $this->request['tipo_usuarios_id'] = $typeUser->id;
        }

        $resPerson['identificaciones'] = $resultIdentificaciones;
        $resUser = $this->userService->create($this->request->all());
        $response = $this->responseCreated(['person' => $resPerson,  'user' => $resUser]);
      }
  
      // Verificar si hubo errores de validación de identificaciones
      if ($validationError) {
        DB::rollBack();
        return $this->responseError($combinedErrors, 422);
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
      return $this->responseError(['message' => 'Error al crear el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->userValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->userService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del usuario del usuario', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function updateComplete($id){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $identificaciones = $this->request->input('identificaciones');
      $resultIdentificaciones = [];

      $dataPerson = $this->request->all();
      unset($dataPerson['identificaciones']);
      $this->personValidator->setRequest($dataPerson, $this->request->input('personas_id'));
      $validatorPerson = $this->personValidator->validate();
      $validator = $this->userValidator->validate();

      $combinedErrors = [];
      $validationError = false; // Variable de control
        
      if ($validatorPerson->fails()) {
        $combinedErrors['person_errors'] = $validatorPerson->errors();
      }
      
      if ($validator->fails()) {
        $combinedErrors['user_errors'] = $validator->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {  

        $resPerson = $this->personService->update($this->request->all(), $this->request->input('personas_id'));
        if(is_null($resPerson)){
          $response = $this->responseError(['message' => 'Error al actualizar persona'], 422);
        } else {          
          if(!empty($identificaciones)){
            foreach($identificaciones as $identity){
              $identity['personas_id'] = $resPerson->id;
              $identity['user_update_id'] = $this->request->input('user_auth_id');
              $this->identificationValidator->setRequest($identity, $identity['id']);
              $validatorIdentity = $this->identificationValidator->validate();
  
              if ($validatorIdentity->fails()) {
                $combinedErrors['person_errors'] = $validatorIdentity->errors();
                $validationError = true;
                break; // Detener el ciclo en caso de error
              } else {
                $resultIdentificaciones[] = $this->identificationService->update($identity, $identity['id']);
              }
            }

            $resPerson->identificaciones = $resultIdentificaciones;
          }
        }
        
        $resUser = $this->userService->update($this->request->all(), $id);
        if(is_null($resUser)){
          $response = $this->responseError(['message' => 'Error al actualizar usuario'], 422);
        }

 
        $response = $this->responseUpdate(['person' => $resPerson,  'user' => $resUser]);
      }

      // Verificar si hubo errores de validación de identificaciones
      if ($validationError) {
        DB::rollBack();
        return $this->responseError($combinedErrors, 422);
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
      return $this->responseError(['message' => 'Error al modificar el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try {
      $result = $this->userService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el usuario', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try {
      $result = $this->userService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el usuario', 'error' => $e->getMessage()], 500);
    }
  }
}