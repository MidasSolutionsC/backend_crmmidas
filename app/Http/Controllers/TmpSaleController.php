<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Implementation\TmpSaleService;
use App\Validator\TmpSaleValidator;

class TmpSaleController extends Controller{
  private $request;
  private $tmpSaleService;
  private $tmpSaleValidator;

   public function __construct(
     Request $request, 
     TmpSaleService $tmpSaleService, 
     TmpSaleValidator $tmpSaleValidator,
     )
   {
     $this->request = $request;
     $this->tmpSaleService = $tmpSaleService;
     $this->tmpSaleValidator = $tmpSaleValidator;
   }
 
   public function listAll(){
     try{
       $result = $this->tmpSaleService->getAll();
       $response = $this->response();
   
       if($result != null){
         $response = $this->response($result);
       } 
   
       return $response;
     } catch(\Exception $e){
       return $this->responseError(['message' => 'Error al listar las ventas', 'error' => $e->getMessage()], 500);
     }
   }
 
   public function get($id){
     try{
       $result = $this->tmpSaleService->getById($id);
       $response = $this->response();
   
       if($result != null){
         $response = $this->response([$result]);
       } 
   
       return $response;
     } catch(\Exception $e){
       return $this->responseError(['message' => 'Error al obtener los datos de la venta', 'error' => $e->getMessage()], 500);
     }
   }
 
   public function create(){
     try{
       $validator = $this->tmpSaleValidator->validate();
   
       if($validator->fails()){
         $response = $this->responseError($validator->errors(), 422);
       } else {
         $result = $this->tmpSaleService->create($this->request->all());
         $response = $this->responseCreated([$result]);
       }
   
       return $response;
     } catch(\Exception $e){
       return $this->responseError(['message' => 'Error al crear la venta', 'error' => $e->getMessage()], 500);
     }
   }
 
   public function update($id){
     try{
       $validator = $this->tmpSaleValidator->validate();
   
       if($validator->fails()){
         $response = $this->responseError($validator->errors(), 422);
       } else {
         $result = $this->tmpSaleService->update($this->request->all(), $id);
         if($result != null){
           $response = $this->responseUpdate([$result]);
         } else {
           $response = $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $result]);
         }
       }
   
       return $response;
     } catch(\Exception $e){
       return $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $e->getMessage()], 500);
     }
   }
 
   public function delete($id){
     try{
       $result = $this->tmpSaleService->delete($id);
       if($result){
         $response = $this->responseDelete([$result]);
       } else {
         $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
       }
   
       return $response;
     } catch(\Exception $e){
       return $this->responseError(['message' => 'Error al eliminar la venta', 'error' => $e->getMessage()], 500);
     }
   }
 
   public function restore($id){
     try{
       $result = $this->tmpSaleService->restore($id);
       if($result){
         $response = $this->responseRestore([$result]);
       } else {
         $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
       }
   
       return $response;
     } catch(\Exception $e){
       return $this->responseError(['message' => 'Error al restaurar la venta', 'error' => $e->getMessage()], 500);
     }
     
   }
}