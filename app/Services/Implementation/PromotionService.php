<?php

namespace App\Services\Implementation;

use App\Models\Promotion;
use App\Services\Interfaces\IPromotion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class PromotionService implements IPromotion{

  private $model;

  public function __construct()
  {
    $this->model = new Promotion();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = Promotion::query();
    $query->select(
      'promociones.*', 
      'TS.nombre as tipo_servicios_nombre'
    );
    
    $query->leftJoin('tipo_servicios as TS', 'promociones.tipo_servicios_id', '=', 'TS.id');


    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('promociones.nombre', 'LIKE', "%$search%")
              ->orWhere('promociones.descripcion', 'LIKE', "%$search%")
              ->orWhere('promociones.descuento', 'LIKE', "%$search%")
              ->orWhere('TS.nombre', 'LIKE', "%$search%");
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
    // $query = $this->model->select();
    $query = $this->model->select(
      'promociones.*', 
      'tipo_servicios.nombre as tipo_servicios_nombre'
      )->join('tipo_servicios', 'promociones.tipo_servicios_id', '=', 'tipo_servicios.id');
    $result = $query->get();
    return $result;
  }

  public function search(array $data){
    $search = $data['search'];
    $brandId = !empty($data['marcas_id'])? $data['marcas_id']: null;
    $typeProduct = !empty($data['tipo_producto'])? $data['tipo_producto']: null;
    $typeServiceId = !empty($data['tipo_servicios_id'])? $data['tipo_servicios_id']: null;
    $len = !empty($data['len'])? $data['len']: 20;
    $order = !empty($data['order'])? $data['order']: 'desc';
    
    $query = $this->model->query();
    // $query->with(['typeCurrency:id,nombre,iso_code,simbolo']);
    $query->select(
      'promociones.*', 
      // 'TS.nombre as tipo_servicios_nombre', 
      'TM.id as tipo_monedas_id',
      'TM.nombre as tipo_monedas_nombre',
      'TM.simbolo as tipo_monedas_simbolo',
      'TM.iso_code as tipo_monedas_iso_code',
    );

    $query->leftJoin('tipo_monedas as TM', 'promociones.tipo_monedas_id', '=', 'TM.id');

    if(!is_null($typeProduct)){
      $query->where("tipo_producto", $typeProduct);
    }

    if(!is_null($typeServiceId)){
      $query->where("tipo_servicios_id", $typeServiceId);
    }
    
    if(!is_null($brandId)){
      $query->where("marcas_id", $brandId);
    }

    $query->whereRaw("promociones.nombre like ?", ['%' . $search . '%']);
    $query->orderBy('id', $order);
    $query->take($len); // Limite de resultados
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()->where('nombre', $data['nombre'])->whereNotNull('deleted_at')->first();
    $promotion = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $promotion = $existingRecord;
      }
    } else {
      $data['created_at'] = Carbon::now(); 
      $data['user_create_id'] = $data['user_auth_id'];
      $promotion = $this->model->create($data);
      if($promotion){
        $promotion->created_at = Carbon::parse($promotion->created_at)->format('Y-m-d H:i:s');
      }
      
    }
    
    // Obtener el nombre del tipo de servicio relacionado
    if ($promotion->typeService) {
      $promotion->tipo_servicios_nombre = $promotion->typeService->nombre;
    }

    return $promotion;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $promotion = $this->model->find($id);
    if($promotion){
      $promotion->fill($data);
      $promotion->save();
      $promotion->updated_at = Carbon::parse($promotion->updated_at)->format('Y-m-d H:i:s');
      // Comprobar si existe una referencia a la tabla typeService
      if ($promotion->typeService) {
        $promotion->tipo_servicios_nombre = $promotion->typeService->nombre;
      }
      return $promotion;
    }

    return null;
  }

  public function delete(int $id){
    $promotion = $this->model->find($id);
    if($promotion != null){
      $promotion->is_active = 0;
      $promotion->save();
      $result = $promotion->delete();
      if($result){
        $promotion->deleted_st = Carbon::parse($promotion->deleted_at)->format('Y-m-d H:i:s');
        return $promotion;
      }
    }

    return false;
  }

  public function restore(int $id){
    $promotion = $this->model->withTrashed()->find($id);
    if($promotion != null && $promotion->trashed()){
      $promotion->is_active = 1;
      $promotion->save();
      $result = $promotion->restore();
      if($result){
        return $promotion;
      }
    }

    return false;
  }

}

?>