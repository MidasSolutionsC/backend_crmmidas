<?php
namespace App\Http\Controllers;

use App\Services\Implementation\TmpInstallationService;
use Illuminate\Http\Request;
use App\Services\Implementation\TmpSaleDetailService;
use App\Services\Implementation\TmpSaleService;
use App\Validator\TmpInstallationValidator;
use App\Validator\TmpSaleDetailValidator;
use App\Validator\TmpSaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\isEmpty;

class TmpSaleDetailController extends Controller{
  private $request;
  private $tmpSaleDetailService;
  private $tmpSaleDetailValidator;

  private $tmpSaleService;
  private $tmpSaleValidator;

  private $tmpInstallationService;
  private $tmpInstallationValidator;

  public function __construct(
    Request $request, 
    TmpSaleDetailService $tmpSaleDetailService, 
    TmpSaleDetailValidator $tmpSaleDetailValidator,
    TmpSaleService $tmpSaleService,
    TmpSaleValidator $tmpSaleValidator,
    TmpInstallationService $tmpInstallationService,
    TmpInstallationValidator $tmpInstallationValidator
    )
  {
    $this->request = $request;
    $this->tmpSaleDetailService = $tmpSaleDetailService;
    $this->tmpSaleDetailValidator = $tmpSaleDetailValidator;
    $this->tmpSaleService = $tmpSaleService;
    $this->tmpSaleValidator = $tmpSaleValidator;
    $this->tmpInstallationService = $tmpInstallationService;
    $this->tmpInstallationValidator = $tmpInstallationValidator;
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->tmpSaleDetailService->index($data);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los detalles', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->tmpSaleDetailService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los detalles de ventas', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function getFilterBySale($saleId){
    try{
      $result = $this->tmpSaleDetailService->getFilterBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los detalles de ventas', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->tmpSaleDetailService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del detalles de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->tmpSaleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpSaleDetailService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->tmpSaleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpSaleDetailService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del detalle de la venta', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      // Obtener los servicios
      $serviceIds = $this->request->input('servicios_ids');
      // Crear detalle 
      $resSaleDetail = [];
      $resErrors = [];

      $reqSaleDetail = $this->request->all();
      $reqSaleDetail["user_create_id"] = $this->request->input('user_auth_id');

      foreach($serviceIds as $serviceId){
        $reqSaleDetail['servicios_id'] = $serviceId;
        $this->tmpSaleDetailValidator->setRequest($reqSaleDetail);
        $validatorSaleDetail = $this->tmpSaleDetailValidator->validate();
        if ($validatorSaleDetail->fails()) {
          $resErrors = $validatorSaleDetail->errors();
          DB::rollBack();
          break;
        } else {
          $resSaleDetail[] = $this->tmpSaleDetailService->create($reqSaleDetail);
        }
      }

      if(!empty($resErrors)){
        $response = $this->responseError($resErrors, 422);
      } else {
        $resData = ["ventas_detalles" => $resSaleDetail, "errors" => $resErrors];
        $response = $this->responseCreated($resData);
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
      return $this->responseError(['message' => 'Error al crear el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->tmpSaleDetailService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->tmpSaleDetailService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
    
  }
}