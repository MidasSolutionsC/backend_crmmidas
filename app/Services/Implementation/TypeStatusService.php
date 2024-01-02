<?php 

namespace App\Services\Implementation;

use App\Models\TypeStatus;
use App\Services\Interfaces\ITypeStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TypeStatusService implements ITypeStatus{
  
  private $model;

  public function __construct() {
    $this->model = new TypeStatus();
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

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function getByName(string $name){
    $query = $this->model->select();
    $result = $query->whereRaw('LOWER(nombre) = ?', [strtolower($name)])->first();
    return $result;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()->where('nombre', $data['nombre'])->whereNotNull('deleted_at')->first();
    $typeStatus = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $typeStatus = $existingRecord;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $data['created_at'] = Carbon::now(); 
      if(isset($data['user_auth_id'])){
        $data['user_create_id'] = $data['user_auth_id'];
      }

      $typeStatus = $this->model->create($data);
      if($typeStatus){
        $typeStatus->created_at = Carbon::parse($typeStatus->created_at)->format('Y-m-d H:i:s');
      }
    }
    
    return $typeStatus;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $typeStatus = $this->model->find($id);
    if($typeStatus){
      $typeStatus->fill($data);
      $typeStatus->save();
      $typeStatus->updated_at = Carbon::parse($typeStatus->updated_at)->format('Y-m-d H:i:s');
      return $typeStatus;
    }

    return null;
  }

  public function delete(int $id){
    $typeStatus = $this->model->find($id);
    if($typeStatus != null){
      $typeStatus->is_active = 0;
      $typeStatus->save();
      $result = $typeStatus->delete();
      if($result){
        $typeStatus->deleted_st = Carbon::parse($typeStatus->deleted_at)->format('Y-m-d H:i:s');
        return $typeStatus;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeStatus = $this->model->withTrashed()->find($id);
    if($typeStatus != null && $typeStatus->trashed()){
      $typeStatus->is_active = 1;
      $typeStatus->save();
      $result = $typeStatus->restore();
      if($result){
        return $typeStatus;
      }
    }

    return false;
  }

}


?>