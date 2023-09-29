<?php

namespace App\Http\Controllers;

use App\Models\Installation;
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
        $response = $this->responseError(["messafe" => "no se especifico id de la venta"], 422);
        DB::rollBack();
      }

      // Filtra los registros de TemporalCliente que deseas copiar
      // $tmpInstallations = TmpInstallation::where('ventas_id', '=', $saleId)->get();
      // $tmpSaleDetails = TmpSaleDetail::where('ventas_id', '=', $saleId)->get();

      // foreach ($tmpInstallations as $data) {
      //   $installation = new Installation($data->toArray());
      //   $installation->save();
      // }

      // foreach ($tmpSaleDetails as $data) {
      //   $saleDetail = new SaleDetail($data->toArray());
      //   $saleDetail->save();
      // }
      

      // Copiar datos de temp_clientes a clientes
      DB::statement('INSERT INTO ventas 
        (clientes_id, fecha, hora, comentario, user_create_id, is_active)
      SELECT clientes_id, fecha, hora, comentario, user_create_id, is_active
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

      // Copiar datos de tmp_instalaciones a instalaciones con el nuevo ID de venta
      DB::statement('INSERT INTO ventas_detalles 
        (ventas_id, tipo, direccion, numero, escalera, portal, planta, puerta, codigo_postal, localidad, provincia, is_active, user_create_id)
        SELECT ?, tipo, direccion, numero, escalera, portal, planta, puerta, codigo_postal, localidad, provincia, is_active, user_create_id
      FROM tmp_instalaciones WHERE ventas_id = ?', [$lastSaleId, $saleId]);


      // // Copiar datos de tmp_instalaciones a instalaciones con el nuevo ID de venta
      // DB::statement('INSERT INTO instalaciones 
      //   (ventas_id, tipo, direccion, numero, escalera, portal, planta, puerta, codigo_postal, localidad, provincia, is_active, user_create_id)
      //   SELECT ?, tipo, direccion, numero, escalera, portal, planta, puerta, codigo_postal, localidad, provincia, is_active, user_create_id
      // FROM tmp_instalaciones WHERE ventas_id = ?', [$lastSaleId, $saleId]);


      // Obtener los IDs de las instalaciones recién insertados
      $installationIds = DB::table('instalaciones')->pluck('id');


      // foreach ($installationIds as $installationId) {
      //   // Reemplaza 'detalle_venta' y 'campo_instalacion' con los nombres reales de tu tabla y columna
      //   DB::table('detalle_venta')->insert([
      //       'venta_id' => $lastSaleId, // ID de venta
      //       'instalacion_id' => $installationId, // ID de instalación
      //   ]);
      // }

      // DB::statement('UPDATE ventas_detalles
      // SET id_instalacion = ?,
      //     id_venta = ?
      // WHERE id_instalacion IN (?)', [$lastSaleId, $lastSaleId, $installationIds]);


      // // Copiar datos de temp_pedidos a pedidos
      // DB::statement('INSERT INTO ventas_detalles (cliente_id, fecha, ...)
      // SELECT cliente_id, fecha, ...
      // FROM tmp_');

      // Copiar datos de temp_pedidos_productos a pedidos_productos (tabla pivote)
      // DB::statement('INSERT INTO venta_detalles 
      //   (ventas_id, servicios_id, user_create_id, observacion, fecha_cierre, datos_json, tipo_estados_id, is_active)

      // SELECT ?, TVD.servicios_id, TVD.user_create_id, TVD.observacion, TVD.fecha_cierre, TVD.datos_json, TVD.tipo_estados_id, TVD.is_active
      // FROM tmp_ventas_detalles TVD
      // INNER JOIN temp_pedidos t ON tp.pedido_id = t.id
      // INNER JOIN clientes c ON t.cliente_id = c.id
      // WHERE c.id IN (' . implode(',', $installationIds) . ')');


      $response = $this->responseCreated(["ventas_id" => $lastSaleId, "installations" => $installationIds]);
      
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
