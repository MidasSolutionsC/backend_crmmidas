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

      // Obtener las instalaciones
      $installation = $this->request->input('instalacion');

      // Obtener los servicios
      $serviceIds = $this->request->input('servicios_ids');

      // Obtener el detalle de la venta
      $ventasId = $this->request->input('ventas_id');
      $saleDetail = $this->request->input('detalle_venta');
      

      // Actualizar datos del request
      $this->tmpInstallationValidator->setRequest($installation);
      $this->tmpSaleDetailValidator->setRequest($saleDetail);

      $validatorInstallation = $this->tmpInstallationValidator->validate();
      
      $validatorSaleDetail = $this->tmpSaleDetailValidator->validate();

      // Errores obtenidos
      $combinedErrors = [];
        
      if ($validatorInstallation->fails()) {
        $combinedErrors['installation_errors'] = $validatorInstallation->errors();
      }
      
      if ($validatorSaleDetail->fails()) {
        $combinedErrors['sale_detail_errors'] = $validatorSaleDetail->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {
        // Validar la venta Temporal
        $validatorSale = $this->tmpSaleValidator->validate();
        if($validatorSale->fails()){
          $combinedErrors['sale_errors'] = $validatorSale->errors();
          $response = $this->responseError($combinedErrors, 422);
        } else {
          $resData = [];
          // Crear la venta Temporal
          $reqSale = [
            "comentario" => "pendiente",
            "user_create_id" => $this->request->input('user_auth_id')
          ];
          
          $saleId = null;
          if(is_null($ventasId)){
            $resSale = $this->tmpSaleService->create($reqSale);
            if($resSale){
              $saleId = $resSale->id;
            }
          } else {
            $saleId = $ventasId;
          }

          $resData['ventas_id'] = $saleId;
          
          if(!is_null($saleId)){
            // Crear instalación
            $installation["ventas_id"] = $saleId;
            $installation["user_create_id"] = $this->request->input('user_auth_id');
            $resInstallation = $this->tmpInstallationService->create($installation);
            $installationId = null;
            if($resInstallation){
              $installationId = $resInstallation->id;
            }
            

            // Crear detalle 
            $resSaleDetail = [];
            foreach($serviceIds as $serviceId){
              $reqSaleDetail["user_create_id"] = $this->request->input('user_auth_id');
              $reqSaleDetail = ["ventas_id" => $saleId, "servicios_id" => $serviceId];
  
              if(is_null($installationId)){
                $reqSaleDetail['instalacines_id'] = $installationId;
              } 
  
              $resSaleDetail[] = $this->tmpSaleDetailService->create($reqSaleDetail);
            }
            
            $resData['instalaciones_id'] = $installationId;
            $resData['ventas_detalles'] = $resSaleDetail;
          }

          $response = $this->responseCreated($resData);
        }
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