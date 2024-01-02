<?php

namespace App\Http\Controllers;

use App\Services\Implementation\SaleDetailService;
use App\Services\Implementation\SaleDocumentService;
use App\Services\Implementation\SaleHistoryService;
use Illuminate\Http\Request;
use App\Services\Implementation\SaleService;
use App\Services\Implementation\TypeStatusService;
use App\Validator\SaleDetailValidator;
use App\Validator\SaleDocumentValidator;
use App\Validator\SaleHistoryValidator;
use App\Validator\SaleValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{

  private $request;
  private $saleService;
  private $saleValidator;

  // Venta detalle
  private $saleDetailService;
  private $saleDetailValidator;
  // Venta documentos
  private $saleDocumentService;
  private $saleDocumentValidator;
  // Venta historial
  private $saleHistoryService;
  private $saleHistoryValidator;

  // ESTADOS
  private $typeStatusService;

  public function __construct(
    Request $request,
    SaleService $saleService,
    SaleValidator $saleValidator,
    SaleDetailService $saleDetailService,
    SaleDetailValidator $saleDetailValidator,
    SaleDocumentService $saleDocumentService,
    SaleDocumentValidator $saleDocumentValidator,
    SaleHistoryService $saleHistoryService,
    SaleHistoryValidator $saleHistoryValidator,
    TypeStatusService $typeStatusService,
  ) {
    $this->request = $request;
    $this->saleService = $saleService;
    $this->saleValidator = $saleValidator;
    $this->saleDetailService = $saleDetailService;
    $this->saleDetailValidator = $saleDetailValidator;
    $this->saleDocumentService = $saleDocumentService;
    $this->saleDocumentValidator = $saleDocumentValidator;
    $this->saleHistoryService = $saleHistoryService;
    $this->saleHistoryValidator = $saleHistoryValidator;
    $this->typeStatusService = $typeStatusService;
  }

  public function index()
  {
    try {
      $data = $this->request->input('data');
      $data = json_decode($data, true);

      $result = $this->saleService->index($data);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response($result);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al listar las ventas', 'error' => $e->getMessage()], 500);
    }
  }

  public function listAll()
  {
    try {
      $result = $this->saleService->getAll();
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
      $result = $this->saleService->getById($id);
      $response = $this->response();

      if ($result != null) {
        $response = $this->response([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al obtener los datos de la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function getWithAllReference($id)
  {
    try {
      $result = $this->saleService->getByIdWithAll($id);
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
      $validator = $this->saleValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleService->create($this->request->all());
        $response = $this->responseCreated([$result]);
      }

      return $response;
    } catch (\Exception $e) {
      return $this->responseError(['message' => 'Error al crear la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function update($id)
  {
    try {
      $validator = $this->saleValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleService->update($this->request->all(), $id);
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

  public function createComplete()
  {
    try {
      // Iniciar una transacción
      DB::beginTransaction();

      $validatorSale = $this->saleValidator->validate();
      $validatorDetail = $this->saleDetailValidator->validate();

      $combinedErrors = [];

      if ($validatorSale->fails()) {
        $combinedErrors['sale_errors'] = $validatorSale->errors();
      }

      if ($validatorDetail->fails()) {
        $combinedErrors['sale_detail_errors'] = $validatorDetail->errors();
      }

      if (!empty($combinedErrors)) {
        $response = $this->responseError($combinedErrors, 422);
      } else {
        $resSale = $this->saleService->create($this->request->all());
        if ($resSale) {
          $this->request['ventas_id'] = $resSale->id;
          // Obtener el nombre del tipo de servicio relacionado
          $resSale->tipo_servicios_nombre = $resSale->typeService->nombre;
          // Ahora, obtén el precio más reciente del producto
        } else {
          $response = $this->responseError(['message' => 'Erro al obtener el id venta'], 422);
        }

        // Registrar detalle
        $resDetail = $this->saleService->create($this->request->all());

        // Obtener el precio más reciente
        $resSale->precio = $resSale->getLastPrice();
        $response = $this->responseCreated(['sale' => $resSale, 'saleDetail' => $resDetail]);
      }


      // Si todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch (\Exception $e) {
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al crear el producto', 'error' => $e->getMessage()], 500);
    }
  }

  public function finalProcess($id)
  {
    try {
      // Iniciar una transacción
      DB::beginTransaction();

      $clientId = $this->request->input('clientes_id');

      if (empty($clientId)) {
        $response = $this->responseError(["message" => ["no se especifico id del cliente"]], 422);
        DB::rollBack();
      }

      // $saleAfter = (array)$this->saleService->getById($id);
      $saleAfter = json_decode(json_encode($this->saleService->getById($id)), true);

      $saleCurrent = $this->request->all();
      $dataMerge = array_merge($saleAfter, $saleCurrent);
      // $dataMerge = array_merge($saleAfter, $saleCurrent, ['nro_orden' => $saleAfter['nro_orden']]);

      // return $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $saleAfter]);

      $this->saleValidator->setRequest($dataMerge, $id);
      $validator = $this->saleValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $typeStatus = $this->typeStatusService->getByName('Pendiente');
        $dataSave = $this->request->all();
        $dataSave['tipo_estados_id'] = $typeStatus->id;

        $result = $this->saleService->update($dataSave, $id);
        if ($result != null) {
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $result]);
        }
      }

      // SFFi todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      // Maneja la excepción, por ejemplo, muestra un mensaje de error
      DB::rollBack();
      return $this->responseError(['message' => 'Error al finalizar la venta', 'error' => $e->getMessage()], 422);
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

  public function cancelProcess($id)
  {
    try {
      // Iniciar una transacción
      DB::beginTransaction();

      // $saleAfter = (array)$this->saleService->getById($id);
      $saleAfter = json_decode(json_encode($this->saleService->getById($id)), true);

      $saleCurrent = $this->request->all();
      $dataMerge = array_merge($saleAfter, $saleCurrent, ['is_active' => 0]);

      $this->saleValidator->setRequest($dataMerge, $id);
      $validator = $this->saleValidator->validate();

      if ($validator->fails()) {
        $response = $this->responseError($validator->errors(), 422);
      } else {
        $result = $this->saleService->update($this->request->all(), $id);
        if ($result != null) {
          $response = $this->responseUpdate([$result]);
        } else {
          $response = $this->responseError(['message' => 'Error al actualizar los datos de la venta', 'error' => $result]);
        }
      }

      // SFFi todo está bien, confirmar la transacción
      DB::commit();
      return $response;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      // Maneja la excepción, por ejemplo, muestra un mensaje de error
      DB::rollBack();
      return $this->responseError(['message' => 'Error al cancelar la venta', 'error' => $e->getMessage()], 422);
    } catch (ValidationException $e) {
      // Si hay errores de validación, revertir la transacción y devolver los errores
      DB::rollBack();
      return $this->responseError(['message' => 'Error en la validación de datos.', 'error' => $e->validator->getMessageBag()], 422);
    } catch (\Exception $e) {
      // Si hay un error inesperado, revertir la transacción
      DB::rollBack();
      return $this->responseError(['message' => 'Error al cancelar la venta', 'error' => $e->getMessage()], 500);
    }
  }

  public function delete($id)
  {
    try {
      $result = $this->saleService->delete($id);
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
      $result = $this->saleService->restore($id);
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
