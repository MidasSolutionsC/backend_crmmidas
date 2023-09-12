<?php

namespace App\Services\Implementation;

use App\Models\Brand;
use App\Services\Interfaces\IBrand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService implements IBrand{

  private $model;

  public function __construct()
  {
    $this->model = new Brand();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = Brand::query();
    $query->select();
    

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('marcas.nombre', 'LIKE', "%$search%")
              ->orWhere('marcas.descripcion', 'LIKE', "%$search%");
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

  public function getById(int $id){
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()
    ->where('nombre', $data['nombre'])
    ->whereNotNull('deleted_at')->first();
    $brand = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      if(isset($data['user_auth_id'])){
        $existingRecord->user_update_id = $data['user_auth_id'];
      }
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $brand = $existingRecord;
      }
    } else {
      $data['created_at'] = Carbon::now(); 
      if(isset($data['user_auth_id'])){
        $data['user_create_id'] = $data['user_auth_id'];
      }
      $brand = $this->model->create($data);
    }

    return $brand;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id']; 
    }
    $brand = $this->model->find($id);
    if($brand){
      $brand->fill($data);
      $brand->save();
      $brand->updated_at = Carbon::parse($brand->updated_at)->format('Y-m-d H:i:s');
      return $brand;
    }

    return null;
  }

  public function delete(int $id){
    $brand = $this->model->find($id);
    if($brand != null){
      $brand->is_active = 0;
      $brand->save();
      $result = $brand->delete();
      if($result){
        $brand->deleted_st = Carbon::parse($brand->deleted_at)->format('Y-m-d H:i:s');
        return $brand;
      }
    }

    return false;
  }

  public function restore(int $id){
    $brand = $this->model->withTrashed()->find($id);
    if($brand != null && $brand->trashed()){
      $brand->is_active = 1;
      $brand->save();
      $result = $brand->restore();
      if($result){
        return $brand;
      }
    }

    return false;
  }

}


?>