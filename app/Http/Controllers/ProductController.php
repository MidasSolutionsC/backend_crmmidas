<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\Implementation\ProductPriceService;
use App\Services\Implementation\ProductService;
use App\Validator\ProductPriceValidator;
use App\Validator\ProductValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller{

  private $request;
  private $productService;
  private $productValidator;

  private $productPriceService;
  private $productPriceValidator;

  public function __construct(Request $request, ProductService $productService, ProductValidator $productValidator, ProductPriceService $productPriceService, ProductPriceValidator $productPriceValidator)
  {
    $this->request = $request;
    $this->productService = $productService;
    $this->productValidator = $productValidator;
    $this->productPriceService = $productPriceService;
    $this->productPriceValidator = $productPriceValidator;
  }

  public function index(){
    try{
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->productService->index($data);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los productos', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll(){
    try{
      $result = $this->productService->getAll();
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los productos', 'error' => $e->getMessage()], 500);
    }
  }

  public function search(){
    try{
      $result = $this->productService->search($this->request->all());
      $response = $this->response();
  
      if($result != null){
        $response = $this->response($result);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al listar los productos', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id){
    try{
      $result = $this->productService->getById($id);
      $response = $this->response();
  
      if($result != null){
        $response = $this->response([$result]);
      } 
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al obtener los datos del producto', 'error' => $e->getMessage()], 500);
    }
  }

  public function create(){
    try{
      $validator = $this->productValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->productService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al crear el producto', 'error' => $e->getMessage()], 500);
    }
  }
  
  public function update($id){
    try{
      $validator = $this->productValidator->validate();
  
      if($validator->fails()){
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->productService->update($this->request->all(), $id);
        if($result != null){
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos del producto', 'error' => $result]);
        }
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al actualizar los datos del producto', 'error' => $e->getMessage()], 500);
    }
  }

  public function createComplete(){
    try{
      // Iniciar una transacción
      DB::beginTransaction();

      $validatorProduct = $this->productValidator->validate();
      $validatorPrice = $this->productPriceValidator->validate();

      $combinedErrors = [];
        
      if ($validatorProduct->fails()) {
        $combinedErrors['product_errors'] = $validatorProduct->errors();
      }
      
      if ($validatorPrice->fails()) {
        $combinedErrors['price_errors'] = $validatorPrice->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {
        $resProduct = $this->productService->create($this->request->all());
        if($resProduct){
          $this->request['productos_id'] = $resProduct->id;
          // Ahora, obtén el precio más reciente del producto
        } else {
          $response = $this->responseError(['message' => 'Erro al obtener el id producto'], 422);
        }
        
        // Registrar precio
        $resPrice = $this->productPriceService->create($this->request->all());
        if($resPrice){
          // Obtener el precio más reciente
          $resProduct->precio = $resProduct->getLastPrice(); 
        }
        
        $response = $this->responseCreated(['product' => $resProduct, 'productPrice' => $resPrice]);
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
      return $this->responseError(['message' => 'Error al crear el producto', 'error' => $e->getMessage()], 500);
    }
  }

  public function updateComplete($id){
    try{
      // Iniciar una transacción
      DB::beginTransaction();
      
      $validatorProduct = $this->productValidator->validate();
      $validatorPrice = $this->productPriceValidator->validate();

      $combinedErrors = [];
        
      if ($validatorProduct->fails()) {
        $combinedErrors['product_errors'] = $validatorProduct->errors();
      }
      
      if ($validatorPrice->fails()) {
        $combinedErrors['price_errors'] = $validatorPrice->errors();
      }

      if(!empty($combinedErrors)){
        $response = $this->responseError($combinedErrors, 422);
      } else {

        if($this->request->input('tipo_producto') == 'F'){
          $this->request['tipo_servicios_id'] = NULL;
        }

        $resProduct = $this->productService->update($this->request->all(), $id);
        if($resProduct){
          $this->request['productos_id'] = $resProduct->id;
          // Obtener el nombre del tipo de servicio relacionado
          $resProduct->tipo_servicios_nombre = $resProduct->typeService->nombre ?? null;

          // Ahora, obtén el precio más reciente del producto
        } else {
          $response = $this->responseError(['message' => 'Erro al obtener el id producto'], 422);
        }
        
        // Registrar precio
        $resPrice = $this->productPriceService->create($this->request->all());
        
        // Obtener el precio más reciente
        $resProduct->precio = $resProduct->getLastPrice(); 
        $response = $this->responseCreated(['product' => $resProduct, 'productPrice' => $resPrice]);
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
      return $this->responseError(['message' => 'Error al actualizar el producto', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id){
    try{
      $result = $this->productService->delete($id);
      if($result){
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al eliminar el producto', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id){
    try{
      $result = $this->productService->restore($id);
      if($result){
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }
  
      return $response;
    } catch(\Exception $e){
      return $this->responseError(['message' => 'Error al restaurar el producto', 'error' => $e->getMessage()], 500);
    }
    
  }

}