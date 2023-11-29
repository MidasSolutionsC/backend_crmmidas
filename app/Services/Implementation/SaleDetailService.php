<?php

namespace App\Services\Implementation;

use App\Models\SaleDetail;
use App\Services\Interfaces\ISaleDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleDetailService implements ISaleDetail{

  private $model;

  public function __construct()
  {
    $this->model = new SaleDetail();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = SaleDetail::query();

    $query->select(
      'ventas_detalles.*',
      'SR.nombre as servicios_nombre',
      'IT.provincia as instalaciones_provincia',
      'IT.localidad as instalaciones_localidad',
    );

    $query->selectRaw("CONCAT_WS(', ',
      CASE WHEN IT.tipo IS NOT NULL AND IT.tipo != '' THEN CONCAT(IT.tipo, ' ', IT.direccion) ELSE NULL END,
      CASE WHEN IT.numero IS NOT NULL AND IT.numero != '' THEN CONCAT(' N° ', IT.numero) ELSE NULL END,
      CASE WHEN IT.escalera IS NOT NULL AND IT.escalera != '' THEN IT.escalera ELSE NULL END,
      CASE WHEN IT.portal IS NOT NULL AND IT.portal != '' THEN IT.portal ELSE NULL END,
      CASE WHEN IT.planta IS NOT NULL AND IT.planta != '' THEN IT.planta ELSE NULL END,
      CASE WHEN IT.puerta IS NOT NULL AND IT.puerta != '' THEN IT.puerta ELSE NULL END
    ) as instalaciones_direccion_completo");

    $query->join('ventas as VT', 'ventas_detalles.ventas_id', 'VT.id');
    $query->leftJoin('instalaciones as IT', 'ventas_detalles.instalaciones_id', 'IT.id');
    $query->join('servicios as SR', 'ventas_detalles.servicios_id', 'SR.id');


    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('IT.localidad', 'LIKE', "%$search%")
              ->orWhere('IT.provincia', 'LIKE', "%$search%")
              ->orWhere('SR.nombre', 'LIKE', "%$search%");
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
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }


  public function getFilterBySale(int $saleId){
    $query = $this->model->query();

    // $query->with(['product:id,nombre,tipo_servicios_id','product.typeService:id,nombre', 'promotion', 'typeStatus']);
    $query->with([
      'product.latestPrice.typeCurrency:id,nombre,iso_code,simbolo',
      'product.typeService:id,nombre,icono', 
      'promotion.typeCurrency:id,nombre,iso_code,simbolo', 
      'typeStatus',
      'installation'
    ]);
    $query->select();

    // $query->select(
    //   'ventas_detalles.*',
    //   'TS.id as tipo_servicios_id',
    //   'TS.nombre as tipo_servicios_nombre',
    //   'SR.nombre as servicios_nombre',
    //   'IT.provincia as instalaciones_provincia',
    //   'IT.localidad as instalaciones_localidad',
    //   'IT.codigo_postal as instalaciones_codigo_postal',
    //   'TE.nombre as tipo_estados_nombre',
    // );

    // $query->selectRaw("CONCAT_WS(', ',
    //   CASE WHEN IT.tipo IS NOT NULL AND IT.tipo != '' THEN CONCAT(IT.tipo, ' ', IT.direccion) ELSE NULL END,
    //   CASE WHEN IT.numero IS NOT NULL AND IT.numero != '' THEN CONCAT(' N° ', IT.numero) ELSE NULL END,
    //   CASE WHEN IT.escalera IS NOT NULL AND IT.escalera != '' THEN IT.escalera ELSE NULL END,
    //   CASE WHEN IT.portal IS NOT NULL AND IT.portal != '' THEN IT.portal ELSE NULL END,
    //   CASE WHEN IT.planta IS NOT NULL AND IT.planta != '' THEN IT.planta ELSE NULL END,
    //   CASE WHEN IT.puerta IS NOT NULL AND IT.puerta != '' THEN IT.puerta ELSE NULL END
    // ) as instalaciones_direccion_completo");

    // $query->join('ventas as VT', 'ventas_detalles.ventas_id', 'VT.id');
    // $query->leftJoin('instalaciones as IT', 'ventas_detalles.instalaciones_id', 'IT.id');
    // $query->join('servicios as SR', 'ventas_detalles.servicios_id', 'SR.id');
    // $query->leftJoin('tipo_servicios as TS', 'SR.tipo_servicios_id', 'TS.id');
    // $query->leftJoin('tipo_estados as TE', 'ventas_detalles.tipo_estados_id', 'TE.id');

    if($saleId){
      $query->where('ventas_detalles.ventas_id', $saleId);
    }
    
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create1(array $data){
    $data['created_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }

    $saleDetail = $this->model->create($data);
    if($saleDetail){
      $saleDetail->created_at = Carbon::parse($saleDetail->created_at)->format('Y-m-d H:i:s');
      $saleDetail->load(['product.latestPrice.typeCurrency:id,nombre,iso_code,simbolo','product.typeService:id,nombre', 'promotion', 'typeStatus']);
    }

    return $saleDetail;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()
    ->where('ventas_id', $data['ventas_id'])
    ->where('productos_id', $data['productos_id'])
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

    // $saleDetail->load(['product.latestPrice.typeCurrency:id,nombre,iso_code,simbolo','product.typeService:id,nombre', 'promotion', 'typeStatus']);
    $saleDetail->load(['product.latestPrice.typeCurrency:id,nombre,iso_code,simbolo','product.typeService:id,nombre', 'promotion.typeCurrency:id,nombre,iso_code,simbolo', 'typeStatus']);


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
      $saleDetail->updated_at = Carbon::parse($saleDetail->updated_at)->format('Y-m-d H:i:s');
      // $saleDetail->load(['product.latestPrice.typeCurrency:id,nombre,iso_code,simbolo','product.typeService:id,nombre', 'promotion', 'typeStatus']);
      $saleDetail->load(['product.latestPrice.typeCurrency:id,nombre,iso_code,simbolo','product.typeService:id,nombre', 'promotion.typeCurrency:id,nombre,iso_code,simbolo', 'typeStatus']);
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