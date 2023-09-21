<?php

namespace App\Services\Implementation;

use App\Models\Service;
use App\Services\Interfaces\IService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceService implements IService{

  private $model;

  public function __construct()
  {
    $this->model = new Service();
  }

  
  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda
    $typeServiceId = !empty($data['tipo_servicios_id']) ? $data['tipo_servicios_id']: ""; // Término de búsqueda

    $query = Service::query();
    $query->select(
      'servicios.*',
      'TS.nombre as tipo_servicios_nombre',
      'PR.nombre as productos_nombre',
      'PM.nombre as promociones_nombre',
    );

    $query->join('tipo_servicios as TS', 'servicios.tipo_servicios_id', 'TS.id');
    $query->join('productos as PR', 'servicios.productos_id', 'PR.id');
    $query->leftJoin('promociones as PM', 'servicios.promociones_id', 'PM.id');

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('servicios.nombre', 'LIKE', "%$search%")
              ->orWhere('servicios.descripcion', 'LIKE', "%$search%")
              ->orWhere('TS.nombre', 'LIKE', "%$search%")
              ->orWhere('PR.nombre', 'LIKE', "%$search%")
              ->orWhere('PM.nombre', 'LIKE', "%$search%");
    }

    if(!empty($typeServiceId)){
      $query->where('servicios.tipo_servicios_id', $typeServiceId);
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
      'servicios.*',
      'TS.nombre as tipo_servicios_nombre',
      'PR.nombre as productos_nombre',
      'PM.nombre as promociones_nombre',
    );

    $query->join('tipo_servicios as TS', 'servicios.tipo_servicios_id', 'TS.id');
    $query->join('productos as PR', 'servicios.productos_id', 'PR.id');
    $query->leftJoin('promociones as PM', 'servicios.promociones_id', 'PM.id');
    $result = $query->get();
    return $result;
  }

  public function search(array $data){
    $search = $data['search'];
    $query = $this->model->select();
    $query->where("nombre like ?", ['%' . $search . '%']);
    $query->take(20); // Limite de resultados
    $result = $query->get();
    return $result;
  }


  public function getByTypeService(int $typeServiceId){
    $query = $this->model->select();
    if($typeServiceId){
      $query->where('tipo_servicios_id', $typeServiceId);
    }

    $result = $query->get();
    return $result;
  }

  public function getByPromotion(int $promotionId){
    $query = $this->model->select();
    if($promotionId){
      $query->where('promociones_id', $promotionId);
    }
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now();
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id']; 
    }
    $service = $this->model->create($data);
    if($service){
      $service->created_at = Carbon::parse($service->created_at)->format('Y-m-d H:i:s');
    }

    return $service;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    
    $service = $this->model->find($id);
    if($service){
      $service->fill($data);
      $service->save();
      $service->updated_at = Carbon::parse($service->updated_at)->format('Y-m-d H:i:s');
      return $service;
    }

    return null;
  }

  public function delete(int $id){
    $service = $this->model->find($id);
    if($service != null){
      $service->save();
      $result = $service->delete();
      if($result){
        $service->deleted_at = Carbon::parse($service->deleted_at)->format('Y-m-d H:i:s');
        return $service;
      }
    }

    return false;
  }

  public function restore(int $id){
    $service = $this->model->withTrashed()->find($id);
    if($service != null && $service->trashed()){
      $service->save();
      $result = $service->restore();
      if($result){
        return $service;
      }
    }

    return false;
  }

}

?>