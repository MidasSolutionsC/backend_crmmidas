<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\TmpInstallation;
use App\Models\TmpSaleDetail;
use Illuminate\Http\Request;
use App\Services\Implementation\TmpSaleService;
use App\Validator\TmpSaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TmpSaleController extends Controller
{
  private $request;
  private $tmpSaleService;
  private $tmpSaleValidator;

  public function __construct(
    Request $request,
    TmpSaleService $tmpSaleService,
    TmpSaleValidator $tmpSaleValidator,
  ) {
    $this->request = $request;
    $this->tmpSaleService = $tmpSaleService;
    $this->tmpSaleValidator = $tmpSaleValidator;
  }

  public function listAll()
  {
    try {
      $result = $this->tmpSaleService->getAll();
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al listar las ventas', 'error' => $e->getMessage()], 500);
    }
  }

  public function get($id)
  {
    try {
      $result = $this->tmpSaleService->getById($id);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function create()
  {
    try {
      $validator = $this->tmpSaleValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpSaleService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al crear la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function finalProcess()
  {
    try {
      // Iniciar una transacción
      DB::beginTransaction();
      $resultFull = [];

      $saleId = $this->request->input('ventas_id');
      $clientId = $this->request->input('clientes_id');

      if(empty($saleId)){
        $response = $this->responseError(["message" => ["no se especifico id de la venta"]], 422);
        DB::rollBack();
      }

      if(empty($clientId)){
        $response = $this->responseError(["message" => ["no se especifico id del cliente"]], 422);
        DB::rollBack();
      }

      // Consulta el último registro
      // $latestSale = Sale::latest()->first();
      // $nro_orden = 1;
      // if($latestSale){
      //   $nro_orden = $latestSale->nro_orden + 1;
      // }

      // Copiar datos de temp_clientes a clientes
      DB::statement('INSERT INTO ventas 
        (nro_orden, retailx_id, smart_id, direccion_smart_id, clientes_id, fecha, hora, comentario, user_create_id, is_active, created_at)
      SELECT nro_orden, retailx_id, smart_id, direccion_smart_id, clientes_id, fecha, hora, comentario, user_create_id, is_active, created_at
      FROM tmp_ventas WHERE id = ?', [$saleId]);

      // Obtener el último ID insertado de la tabla ventas
      $lastSaleId = DB::connection()->getPdo()->lastInsertId();

      $tmpInstallations = TmpInstallation::where('ventas_id', $saleId)->get();
      foreach ($tmpInstallations as $data) {
        $data->ventas_id = $lastSaleId;
        $installation = new Installation($data->toArray());
        $installation->save();

        DB::statement("UPDATE tmp_ventas_detalles SET instalaciones_id = ? WHERE id = ?", [$installation->id, $data->instalaciones_id]);
      }


      $tmpSaleDetails = TmpSaleDetail::where('ventas_id', $saleId)->get();
      foreach ($tmpSaleDetails as $data) {
        $data->ventas_id = $lastSaleId;
        $saleDetail = new SaleDetail($data->toArray());
        $saleDetail->save();

        // DOCUMENTOS 
        DB::statement("UPDATE tmp_ventas_documentos SET ventas_detalles_id = ? WHERE ventas_detalles_id = ?", [$saleDetail->id, $data->id]);
        // COMENTARIOS 
        DB::statement("UPDATE tmp_ventas_comentarios SET ventas_detalles_id = ? WHERE ventas_detalles_id = ?", [$saleDetail->id, $data->id]);
        // HISTORIAL 
        DB::statement("UPDATE tmp_ventas_historial SET ventas_detalles_id = ? WHERE ventas_detalles_id = ?", [$saleDetail->id, $data->id]);
      }

      // DOCUMENTOS 
      DB::statement('INSERT INTO ventas_documentos 
        (ventas_id, ventas_detalles_id, documentos_id, nombre, tipo, archivo, user_create_id, user_update_id, user_delete_id, is_active, created_at)
        SELECT ?, ventas_detalles_id, documentos_id, nombre, tipo, archivo, user_create_id, user_update_id, user_delete_id, is_active, created_at
      FROM tmp_ventas_documentos WHERE ventas_id = ?', [$lastSaleId, $saleId]);

      // COMENTARIOS 
      DB::statement('INSERT INTO ventas_comentarios 
        (ventas_id, ventas_detalles_id, comentario, fecha, hora, user_create_id, user_update_id, user_delete_id, is_active, created_at)
        SELECT ?, ventas_detalles_id, comentario, fecha, hora, user_create_id, user_update_id, user_delete_id, is_active, created_at
      FROM tmp_ventas_comentarios WHERE ventas_id = ?', [$lastSaleId, $saleId]);

      // HISTORIAL 
      DB::statement('INSERT INTO ventas_historial 
        (ventas_id, ventas_detalles_id, tipo, tipo_estados_id, fecha, hora, comentario, user_create_id, user_update_id, user_delete_id, is_active, created_at)
        SELECT ?, ventas_detalles_id, tipo, tipo_estados_id, fecha, hora, comentario, user_create_id, user_update_id, user_delete_id, is_active, created_at
      FROM tmp_ventas_historial WHERE ventas_id = ?', [$lastSaleId, $saleId]);


      // ELIMINAR TEMPORALES
      DB::statement('DELETE FROM tmp_ventas_historial WHERE ventas_id = ?', [$saleId]);
      DB::statement('DELETE FROM tmp_ventas_comentarios WHERE ventas_id = ?', [$saleId]);
      DB::statement('DELETE FROM tmp_ventas_documentos WHERE ventas_id = ?', [$saleId]);
      DB::statement('DELETE FROM tmp_ventas_detalles WHERE ventas_id = ?', [$saleId]);
      DB::statement('DELETE FROM tmp_instalaciones WHERE ventas_id = ?', [$saleId]);
      DB::statement('DELETE FROM tmp_ventas WHERE id = ?', [$saleId]);

      $response = $this->responseCreated(["ventas_id" => $lastSaleId, "message" => "Proceso completado"]);
      
      // SFFi todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      // Maneja la excepción, por ejemplo, muestra un mensaje de error
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear la venta', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch (\Exception $e) {
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al completar la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id)
  {
    try {
      $validator = $this->tmpSaleValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->tmpSaleService->update($this->request->all(), $id);
        if ($result != null) {
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $result]);
        }
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id)
  {
    try {
      $result = $this->tmpSaleService->delete($id);
      if ($result) {
        $response = $this->responseDelete([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado no existe o ha sido eliminado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al eliminar la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function restore($id)
  {
    try {
      $result = $this->tmpSaleService->restore($id);
      if ($result) {
        $response = $this->responseRestore([$result]);
      } else {
        $response = $this->responseError(['message' => 'El recurso solicitado ha sido restaurado previamente.']);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al restaurar la venta', 'error' => $e->getMessage()], 500);
    }
  }
}
