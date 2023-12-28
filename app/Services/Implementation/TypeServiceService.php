<?php

namespace App\Services\Implementation;

use App\Models\TypeService;
use App\Services\Interfaces\ITypeService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TypeServiceService implements ITypeService{

  private $model;

  public function __construct()
  {
    $this->model = new TypeService();
  }

  public function index($data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = $this->model->query();


    // Aplicar filtro de búsqueda si se proporciona un término
    $query->where(function ($query) use ($search) {
      if(!empty($search)){
        $query->where('nombre', 'LIKE', "%$search%")
          ->orWhere('descripcion', 'like', "%$search%")
          ->orWhere('created_at', 'like', "%$search%")
          ->orWhere('updated_at', 'like', "%$search%");
      }
    });
  
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

  public function search(array $data){
    $search = $data['search'];
    $query = $this->model->select();
    $query->whereRaw("nombre like ?", ['%' . $search . '%']);
    $query->orderBy('id', 'desc');
    $query->take(20); // Limite de resultados
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
    $typeService = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $typeService = $existingRecord;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $data['created_at'] = Carbon::now(); 
      if(isset($data['user_auth_id'])){
        $data['user_create_id'] = $data['user_auth_id'];
      }

      $typeService = $this->model->create($data);
      if($typeService){
        $typeService->created_at = Carbon::parse($typeService->created_at)->format('Y-m-d H:i:s');
      }
    }
    
    return $typeService;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $typeService = $this->model->find($id);
    if($typeService){
      $typeService->fill($data);
      $typeService->save();
      $typeService->updated_at = Carbon::parse($typeService->updated_at)->format('Y-m-d H:i:s');
      return $typeService;
    }

    return null;
  }

  public function delete(int $id){
    $typeService = $this->model->find($id);
    if($typeService != null){
      $typeService->is_active = 0;
      $typeService->save();
      $result = $typeService->delete();
      if($result){
        $typeService->deleted_st = Carbon::parse($typeService->deleted_at)->format('Y-m-d H:i:s');
        return $typeService;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeService = $this->model->withTrashed()->find($id);
    if($typeService != null && $typeService->trashed()){
      $typeService->is_active = 1;
      $typeService->save();
      $result = $typeService->restore();
      if($result){
        return $typeService;
      }
    }

    return false;
  }

}


?>