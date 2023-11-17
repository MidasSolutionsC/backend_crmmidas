<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Services\Implementation\saleDocumentService;
use App\Services\Implementation\SaleService;
use App\Validator\SaleDocumentValidator;
use App\Validator\SaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Utilities\FileUploader;

class SaleDocumentController extends Controller{

  private $request;
  private $saleDocumentService;
  private $saleDocumentValidator;

  private $saleService;
  private $saleValidator;

  public function __construct(
    Request $request, 
    SaleDocumentService $saleDocumentService, 
    SaleDocumentValidator $saleDocumentValidator,
    SaleService $saleService,
    SaleValidator $saleValidator
    )
  {
    $this->request = $request;
    $this->saleDocumentService = $saleDocumentService;
    $this->saleDocumentValidator = $saleDocumentValidator;
    $this->saleService = $saleService;
    $this->saleValidator = $saleValidator;
  }

  public function listAll(){
    try{
      $result = $this->saleDocumentService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los documentos de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function getFilterBySale($saleId){
    try{
      $result = $this->saleDocumentService->getFilterBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los documentos de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->saleDocumentService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del documento de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->saleDocumentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDocumentService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el documento de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $ventasId = $this->request->input('ventas_id');

      if(empty($ventasId)){
        $latestSale = Sale::latest()->first();
        $nro_orden = 1;
        if($latestSale){
          $nro_orden = $latestSale->nro_orden + 1;
        }
        
        $reqSale = [
          "nro_orden" => $nro_orden,
          "comentario" => "",
          "user_create_id" => $this->request->input('user_auth_id')
        ];

        $resSale = $this->saleService->create($reqSale);
        if($resSale){
          $this->request['ventas_id'] = $resSale->id;
        }
      } 

      $this->saleDocumentValidator->setRequest($this->request->all());
      $validator = $this->saleDocumentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        if($this->request->has('file')){
          $file = $this->request->file('file');
          $fileName = FileUploader::upload($file, 'files/sale/', []);
          $this->request['archivo'] = $fileName;
        }

        $result = $this->saleDocumentService->create($this->request->all());
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
      return $this->responseError(['message' => 'Error al registrar el documento', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id){
    try{
      $validator = $this->saleDocumentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleDocumentService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del documento de la venta', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del documento de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->saleDocumentService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el documento de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->saleDocumentService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el documento de la venta', 'error' => $e->getMessage()], 500);
    }
    
  }

}