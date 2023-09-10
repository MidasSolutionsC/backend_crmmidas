<?php

namespace App\Services\Implementation;

use App\Models\CategoryBrand;
use App\Services\Interfaces\ICategoryBrand;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryBrandService implements ICategoryBrand{

  private $model;

  public function __construct()
  {
    $this->model = new CategoryBrand();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = CategoryBrand::query();
    $query->select();
    
    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('nombre', 'LIKE', "%$search%")
              ->orWhere('descripcion', 'LIKE', "%$search%");
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
    $data['created_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id']; 
    }
    $categoryBrand = $this->model->create($data);
    return $categoryBrand;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id']; 
    }
    $categoryBrand = $this->model->find($id);
    if($categoryBrand){
      $categoryBrand->fill($data);
      $categoryBrand->save();
      return $categoryBrand;
    }

    return null;
  }

  public function delete(int $id){
    $categoryBrand = $this->model->find($id);
    if($categoryBrand != null){
      $categoryBrand->is_active = 0;
      $categoryBrand->save();
      $result = $categoryBrand->delete();
      if($result){
        $categoryBrand->deleted_st = Carbon::parse($categoryBrand->deleted_at)->format('Y-m-d H:i:s');
        return $categoryBrand;
      }
    }

    return false;
  }

  public function restore(int $id){
    $categoryBrand = $this->model->withTrashed()->find($id);
    if($categoryBrand != null && $categoryBrand->trashed()){
      $categoryBrand->is_active = 1;
      $categoryBrand->save();
      $result = $categoryBrand->restore();
      if($result){
        return $categoryBrand;
      }
    }

    return false;
  }

}


?>