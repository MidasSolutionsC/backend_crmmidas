<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TmpSaleDocument;
use App\Services\Implementation\TmpSaleDocumentService;
use App\Services\Implementation\TmpSaleService;
use App\Validator\TmpSaleDocumentValidator;
use App\Utilities\FileUploader;
use App\Validator\TmpSaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TmpSaleDocumentController extends Controller{

  private $request;
  private $tmpSaleDocumentService;
  private $tmpSaleDocumentValidator;

  private $tmpSaleService;
  private $tmpSaleValidator;

  public function __construct(
    Request $request, 
    TmpSaleDocumentService $tmpSaleDocumentService, 
    TmpSaleDocumentValidator $tmpSaleDocumentValidator,
    TmpSaleService $tmpSaleService,
    TmpSaleValidator $tmpSaleValidator,
    )
  {
    $this->request = $request;
    $this->tmpSaleDocumentService = $tmpSaleDocumentService;
    $this->tmpSaleDocumentValidator = $tmpSaleDocumentValidator;
    $this->tmpSaleService = $tmpSaleService;
    $this->tmpSaleValidator = $tmpSaleValidator;
  }

  public function listAll(){
    try{
      $result = $this->tmpSaleDocumentService->getAll();
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
      $result = $this->tmpSaleDocumentService->getFilterBySale($saleId);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del documento de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->tmpSaleDocumentService->getById($id);
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
      $validator = $this->tmpSaleDocumentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        if($this->request->has('file')){
          $file = $this->request->file('file');
          $fileName = FileUploader::upload($file, 'files/sale/', []);
          $this->request['archivo'] = $fileName;
        }

        $result = $this->tmpSaleDocumentService->create($this->request->all());
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
        $reqSale = [
          "comentario" => "pendiente",
          "user_create_id" => $this->request->input('user_auth_id')
        ];

        $resSale = $this->tmpSaleService->create($reqSale);
        if($resSale){
          // $ventasId = $resSale->id;
          $this->request['ventas_id'] = $resSale->id;
        }
      } 


      $validator = $this->tmpSaleDocumentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        if($this->request->has('file')){
          $file = $this->request->file('file');
          $fileName = FileUploader::upload($file, 'files/sale/', []);
          $this->request['archivo'] = $fileName;
        }

        $result = $this->tmpSaleDocumentService->create($this->request->all());
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
      $validator = $this->tmpSaleDocumentValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        if($this->request->has('file')){
          $file = $this->request->file('file');
          $fileName = FileUploader::upload($file, 'files/sale/', []);
          $this->request['archivo'] = $fileName;
        }

        $result = $this->tmpSaleDocumentService->update($this->request->all(), $id);
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
      $result = $this->tmpSaleDocumentService->delete($id);
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
      $result = $this->tmpSaleDocumentService->restore($id);
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