<?php

namespace App\Services\Implementation;

use App\Models\TmpInstallation;
use App\Models\TmpSaleDetail;
use App\Models\TypeService;
use App\Services\Interfaces\ISaleDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TmpSaleDetailService implements ISaleDetail{

  private $model;

  public function __construct()
  {
    $this->model = new TmpSaleDetail();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda
    $ventasId = !isset($data['ventas_id']) ? $data['ventas_id']: NULL; // Término de búsqueda

    $query = TmpSaleDetail::query();

    $query->select(
      'tmp_ventas_detalles.*',
      'SR.nombre as servicios_nombre',
      'IT.provincia as instalaciones_provincia',
      'IT.localidad as instalaciones_localidad',
      'IT.codigo_postal as instalaciones_codigo_postal',
    );

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN IT.tipo IS NOT NULL AND IT.tipo != '' THEN CONCAT(IT.tipo, ' ', IT.direccion) ELSE NULL END,
      CASE WHEN IT.numero IS NOT NULL AND IT.numero != '' THEN CONCAT(' N° ', IT.numero) ELSE NULL END,
      CASE WHEN IT.escalera IS NOT NULL AND IT.escalera != '' THEN IT.escalera ELSE NULL END,
      CASE WHEN IT.portal IS NOT NULL AND IT.portal != '' THEN IT.portal ELSE NULL END,
      CASE WHEN IT.planta IS NOT NULL AND IT.planta != '' THEN IT.planta ELSE NULL END,
      CASE WHEN IT.puerta IS NOT NULL AND IT.puerta != '' THEN IT.puerta ELSE NULL END
    ) as instalaciones_direccion_completo");

    $query->join('tmp_ventas as VT', 'tmp_ventas_detalles.ventas_id', 'VT.id');
    $query->leftJoin('tmp_instalaciones as IT', 'tmp_ventas_detalles.instalaciones_id', 'IT.id');
    $query->join('servicios as SR', 'tmp_ventas_detalles.servicios_id', 'SR.id');


    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('IT.localidad', 'LIKE', "%$search%")
              ->orWhere('IT.provincia', 'LIKE', "%$search%")
              ->orWhere('SR.nombre', 'LIKE', "%$search%");
              
    }

    if(!is_null($ventasId)){
      $query->where('VT.id', $ventasId);
    }
    

    // Handle sorting
    if (!empty($data['column']) && !empty($data['order'])) {
      $column = $data['column'];
      $order = $data['order'];
      $query->orderBy($column, $order);
    }

    $result = $query->paginate($perPage, ['*'], 'page', $page);
    $items = new Collection($result->items());
    $items = $items->map(function ($item, $key) use ($result) {
        $index = ($result->currentPage() - 1) * $result->perPage() + $key + 1;
        $item['index'] = $index;
        return $item;
    });

    $paginator = new LengthAwarePaginator($items, $result->total(), $result->perPage(), $result->currentPage());
    return $paginator;
  }

  public function getAll(){
    $query = $this->model->query();
    $query->select(
      'tmp_ventas_detalles.*',
      'SR.nombre as servicios_nombre',
      'IT.provincia as instalaciones_provincia',
      'IT.localidad as instalaciones_localidad',
      'IT.codigo_postal as instalaciones_codigo_postal',
    );

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN IT.tipo IS NOT NULL AND IT.tipo != '' THEN CONCAT(IT.tipo, ' ', IT.direccion) ELSE NULL END,
      CASE WHEN IT.numero IS NOT NULL AND IT.numero != '' THEN CONCAT(' N° ', IT.numero) ELSE NULL END,
      CASE WHEN IT.escalera IS NOT NULL AND IT.escalera != '' THEN IT.escalera ELSE NULL END,
      CASE WHEN IT.portal IS NOT NULL AND IT.portal != '' THEN IT.portal ELSE NULL END,
      CASE WHEN IT.planta IS NOT NULL AND IT.planta != '' THEN IT.planta ELSE NULL END,
      CASE WHEN IT.puerta IS NOT NULL AND IT.puerta != '' THEN IT.puerta ELSE NULL END
    ) as instalaciones_direccion_completo");

    $query->join('tmp_ventas as VT', 'tmp_ventas_detalles.ventas_id', 'VT.id');
    $query->leftJoin('tmp_instalaciones as IT', 'tmp_ventas_detalles.instalaciones_id', 'IT.id');
    $query->join('servicios as SR', 'tmp_ventas_detalles.servicios_id', 'SR.id');
    $result = $query->get();
    return $result;
  }

  public function getFilterBySale(int $saleId){
    $query = $this->model->query();
    $query->select(
      'tmp_ventas_detalles.*',
      'TS.id as tipo_servicios_id',
      'TS.nombre as tipo_servicios_nombre',
      'SR.nombre as servicios_nombre',
      'IT.provincia as instalaciones_provincia',
      'IT.localidad as instalaciones_localidad',
      'IT.codigo_postal as instalaciones_codigo_postal',
    );

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN IT.tipo IS NOT NULL AND IT.tipo != '' THEN CONCAT(IT.tipo, ' ', IT.direccion) ELSE NULL END,
      CASE WHEN IT.numero IS NOT NULL AND IT.numero != '' THEN CONCAT(' N° ', IT.numero) ELSE NULL END,
      CASE WHEN IT.escalera IS NOT NULL AND IT.escalera != '' THEN IT.escalera ELSE NULL END,
      CASE WHEN IT.portal IS NOT NULL AND IT.portal != '' THEN IT.portal ELSE NULL END,
      CASE WHEN IT.planta IS NOT NULL AND IT.planta != '' THEN IT.planta ELSE NULL END,
      CASE WHEN IT.puerta IS NOT NULL AND IT.puerta != '' THEN IT.puerta ELSE NULL END
    ) as instalaciones_direccion_completo");

    $query->join('tmp_ventas as VT', 'tmp_ventas_detalles.ventas_id', 'VT.id');
    $query->leftJoin('tmp_instalaciones as IT', 'tmp_ventas_detalles.instalaciones_id', 'IT.id');
    $query->join('servicios as SR', 'tmp_ventas_detalles.servicios_id', 'SR.id');
    $query->leftJoin('tipo_servicios as TS', 'SR.tipo_servicios_id', 'TS.id');

    if($saleId){
      $query->where('VT.id', $saleId);
    }
    
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->query();
    $query->select(
      'tmp_ventas_detalles.*',
      'TS.id as tipo_servicios_id',
      'TS.nombre as tipo_servicios_nombre',
      'SR.nombre as servicios_nombre',
      'IT.provincia as instalaciones_provincia',
      'IT.localidad as instalaciones_localidad',
      'IT.codigo_postal as instalaciones_codigo_postal',
    );

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN IT.tipo IS NOT NULL AND IT.tipo != '' THEN CONCAT(IT.tipo, ' ', IT.direccion) ELSE NULL END,
      CASE WHEN IT.numero IS NOT NULL AND IT.numero != '' THEN CONCAT(' N° ', IT.numero) ELSE NULL END,
      CASE WHEN IT.escalera IS NOT NULL AND IT.escalera != '' THEN IT.escalera ELSE NULL END,
      CASE WHEN IT.portal IS NOT NULL AND IT.portal != '' THEN IT.portal ELSE NULL END,
      CASE WHEN IT.planta IS NOT NULL AND IT.planta != '' THEN IT.planta ELSE NULL END,
      CASE WHEN IT.puerta IS NOT NULL AND IT.puerta != '' THEN IT.puerta ELSE NULL END
    ) as instalaciones_direccion_completo");

    $query->join('tmp_ventas as VT', 'tmp_ventas_detalles.ventas_id', 'VT.id');
    $query->leftJoin('tmp_instalaciones as IT', 'tmp_ventas_detalles.instalaciones_id', 'IT.id');
    $query->join('servicios as SR', 'tmp_ventas_detalles.servicios_id', 'SR.id');
    $query->leftJoin('tipo_servicios as TS', 'SR.tipo_servicios_id', 'TS.id');

    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()
    ->where('ventas_id', $data['ventas_id'])
    ->where('servicios_id', $data['servicios_id'])
    ->whereNotNull('deleted_at')->first();
    $saleDetail = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      if(isset($data['user_auth_id'])){
        $existingRecord->user_update_id = $data['user_auth_id'];
      }

      if(isset($data['instalaciones_id']) && $data['instalaciones_id'] > 0){
        $existingRecord->instalaciones_id = $data['instalaciones_id'];
      }

      $existingRecord->fill($data);
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $saleDetail = $existingRecord;
        // $saleDetail->tipo_servicios_nombre = $saleDetail->typeService->nombre;
      }
    } else {
      $data['created_at'] = Carbon::now(); 
      if(isset($data['user_auth_id'])){
        $data['user_create_id'] = $data['user_auth_id'];
      }
      $saleDetail = $this->model->create($data);
      if($saleDetail){
        // $saleDetail->tipo_servicios_nombre = $saleDetail->typeService->nombre;
      }
    }

    return $saleDetail;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    
    $saleDetail = $this->model->find($id);
    if($saleDetail){
      $saleDetail->fill($data);
      $saleDetail->save();
      // Cargar la relación 'service' después de actualizar
      // $saleDetail->load('service');

      $saleDetail->updated_at = Carbon::parse($saleDetail->updated_at)->format('Y-m-d H:i:s');

      $saleDetail->load(['service' => function ($query) {
        $query->select('id', 'nombre', 'tipo_servicios_id'); // Selecciona las columnas deseadas de 'service'
      }]);
      
      $service = $saleDetail->service;
      $saleDetail->servicios_nombre = $service->nombre;

       // Realizar consulta en otra tabla usando el campo foráneo
      $typeService = TypeService::where('id', $service->tipo_servicios_id)
        ->select('id', 'nombre') // Selecciona las columnas que deseas
        ->first();

      $saleDetail->tipo_servicios_nombre = $typeService->nombre;
      
      $installation = TmpInstallation::where('id', $saleDetail->instalaciones_id)
        ->select('*')
        ->selectRaw("CONCAT_WS(', ',
          CASE WHEN tipo IS NOT NULL AND tipo != '' THEN CONCAT(tipo, ' ', direccion) ELSE NULL END,
          CASE WHEN numero IS NOT NULL AND numero != '' THEN CONCAT(' N° ', numero) ELSE NULL END,
          CASE WHEN escalera IS NOT NULL AND escalera != '' THEN escalera ELSE NULL END,
          CASE WHEN portal IS NOT NULL AND portal != '' THEN portal ELSE NULL END,
          CASE WHEN planta IS NOT NULL AND planta != '' THEN planta ELSE NULL END,
          CASE WHEN puerta IS NOT NULL AND puerta != '' THEN puerta ELSE NULL END
        ) as direccion_completo")
        ->first();


        if($installation){
          $saleDetail->instalaciones_direccion_completo = $installation->direccion_completo;
          $saleDetail->instalaciones_codigo_postal = $installation->codigo_postal;
          $saleDetail->instalaciones_localidad = $installation->localidad;
          $saleDetail->instalaciones_provincia = $installation->provincia;
        }

      return $saleDetail;
    }

    return null;
  }

  public function delete(int $id){
    $saleDetail = $this->model->find($id);
    if($saleDetail != null){
      $saleDetail->save();
      $result = $saleDetail->delete();
      if($result){
        $saleDetail->deleted_st = Carbon::parse($saleDetail->deleted_at)->format('Y-m-d H:i:s');
        return $saleDetail;
      }
    }

    return false;
  }

  public function restore(int $id){
    $saleDetail = $this->model->withTrashed()->find($id);
    if($saleDetail != null && $saleDetail->trashed()){
      $saleDetail->save();
      $result = $saleDetail->restore();
      if($result){
        return $saleDetail;
      }
    }

    return false;
  }

}


?>