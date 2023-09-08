<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Services\Implementation\GroupService;
use App\Services\Implementation\MemberService;
use App\Validator\GroupValidator;
use App\Validator\MemberValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller{
  private $request;
  private $groupService;
  private $groupValidator;

  private $memberService;
  private $memberValidator;

  public function __construct(
    Request $request, 
    GroupService $groupService, 
    GroupValidator $groupValidator,
    MemberService $memberService,
    MemberValidator $memberValidator
  ) {
    $this->request = $request;
    $this->groupService = $groupService;
    $this->groupValidator = $groupValidator;
    $this->memberService = $memberService;
    $this->memberValidator = $memberValidator;
    
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->groupService->index($data);
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los grupos', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->groupService->getAll();
      $response = $this->response();
  
      if(!is_null($result)){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los grupos', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->groupService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del grupo', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->groupValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->groupService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el grupo', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $validatorGroup = $this->groupValidator->validate();
      $validatorMember = $this->memberValidator->validate();

      $combinedErrors = [];
        
      if ($validatorGroup->fails()) {
        $combinedErrors['group_errors'] = $validatorGroup->errors();
      }
      
      if ($validatorMember->fails()) {
        $combinedErrors['member_errors'] = $validatorMember->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {
        $this->request['user_create_id'] = $this->request->input('user_auth_id');

        $resGroup = $this->groupService->create($this->request->all());
        if($resGroup){
          $this->request['grupos_id'] = $resGroup->id;
        }

        $integrantes = $this->request->input('integrantes');
        $resMember = [];
        foreach($integrantes as $userId){
          $resMember[] = $this->memberService->create(["grupos_id" => $resGroup->id, "usuarios_id" => $userId]);
        }


        $response = $this->responseCreated(['group' => $resGroup,  'member' => $resMember]);

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
      return $this->responseError(['message' => 'Error al crear el grupo', 'error' => $e->getMessage()], 500);
    }
  }

  public function updateComplete($id){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $validatorGroup = $this->groupValidator->validate();
      $validatorMember = $this->memberValidator->validate();

      $combinedErrors = [];
        
      if ($validatorGroup->fails()) {
        $combinedErrors['group_errors'] = $validatorGroup->errors();
      }
      
      if ($validatorMember->fails()) {
        $combinedErrors['member_errors'] = $validatorMember->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {
        $resGroup = $this->groupService->update($this->request->all(), $id);
        if($resGroup){
          $this->request['grupos_id'] = $resGroup->id;
        }

        $integrantesEliminados = $this->request->input('integrantesEliminados');
        $integrantes = $this->request->input('integrantes');

        $resMemberDeleted = [];
        foreach($integrantesEliminados as $memberId){
          $resMemberDeleted[] = $this->memberService->delete($memberId);
        }
        
        $resMember = [];
        foreach($integrantes as $userId){
          $resMember[] = $this->memberService->create(["grupos_id" => $resGroup->id, "usuarios_id" => $userId]);
        }


        // $response = $this->responseCreated(['group' => $integrantesEliminados]);
        $response = $this->responseUpdate(['group' => $resGroup,  'memberCreate' => $resMember, 'memberDeleted' => $resMemberDeleted]);

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
      return $this->responseError(['message' => 'Error al actualizar el grupo', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->groupValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->groupService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del grupo', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del grupo', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->groupService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el grupo', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->groupService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el grupo', 'error' => $e->getMessage()], 500);
    }
    
  }

}