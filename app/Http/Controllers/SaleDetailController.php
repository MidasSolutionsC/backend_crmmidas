<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Services\Implementation\SaleDetailService;
use App\Services\Implementation\SaleService;
use App\Validator\SaleDetailValidator;
use App\Validator\SaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleDetailController extends Controller{

  private $request;
  private $saleDetailService;
  private $saleDetailValidator;

  private $saleService;
  private $saleValidator;


  public function __construct(
    Request $request, 
    SaleDetailService $saleDetailService, 
    SaleDetailValidator $saleDetailValidator,
    SaleService $saleService,
    SaleValidator $saleValidator

  )
  {
    $this->request = $request;
    $this->saleDetailService = $saleDetailService;
    $this->saleDetailValidator = $saleDetailValidator;
    $this->saleService = $saleService;
    $this->saleValidator = $saleValidator;
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->saleDetailService->index($data);
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
      $result = $this->saleDetailService->getAll();
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
      $result = $this->saleDetailService->getFilterBySale($saleId);
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
      $result = $this->saleDetailService->getById($id);
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
      $validator = $this->saleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDetailService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el detalle de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();
      
      $ventasId = $this->request->input('ventas_id');

      if(empty($ventasId)){
        // Consulta el último registro
        $latestSale = Sale::latest()->first();
        $nro_orden = 1;
        if($latestSale){
          $nro_orden = $latestSale->nro_orden + 1;
        }

        $reqSale = [
          "nro_orden" => $nro_orden,
          "comentario" => "pendiente",
          "user_create_id" => $this->request->input('user_auth_id')
        ];

        $resSale = $this->saleService->create($reqSale);
        if($resSale){
          // $ventasId = $resSale->id;
          $this->request['ventas_id'] = $resSale->id;
        }
      } 

      $validator = $this->saleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDetailService->create($this->request->all());
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
      return $this->responseError(['message' => 'Error al agregar el producto/servicio de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->saleDetailValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDetailService->update($this->request->all(), $id);
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

  public function delete($id){
    try{
      $result = $this->saleDetailService->delete($id);
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
      $result = $this->saleDetailService->restore($id);
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