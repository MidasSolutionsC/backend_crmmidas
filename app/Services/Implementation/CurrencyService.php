<?php

namespace App\Services\Implementation;

use App\Models\Currency;
use App\Services\Interfaces\ICurrency;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CurrencyService implements ICurrency{

  private $model;

  public function __construct()
  {
    $this->model = new Currency();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = Currency::query();
    $query->select(
      'divisas.*', 
      'PA.nombre as paises_nombre'
    );
    
    $query->leftJoin('paises as PA', 'divisas.paises_id', '=', 'PA.id');


    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('divisas.nombre', 'LIKE', "%$search%")
              ->orWhere('divisas.descripcion', 'LIKE', "%$search%")
              ->orWhere('PA.nombre', 'LIKE', "%$search%");
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
    $query = $this->model->select(
        'divisas.*', 
        'PA.nombre as paises_nombre'
      );
      
    $query->leftJoin('paises as PA', 'divisas.paises_id', '=', 'PA.id');
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
    
    $currency = $this->model->create($data);
    if($currency){
      $currency->created_at = Carbon::parse($currency->created_at)->format('Y-m-d H:i:s');
    }

    return $currency;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id']; 
    }

    $currency = $this->model->find($id);
    if($currency){
      $currency->fill($data);
      $currency->save();
      $currency->updated_at = Carbon::parse($currency->updated_at)->format('Y-m-d H:i:s');
      return $currency;
    }

    return null;
  }

  public function delete(int $id){
    $currency = $this->model->find($id);
    if($currency != null){
      $currency->is_active = 0;
      $currency->save();
      $result = $currency->delete();
      if($result){
        $currency->deleted_st = Carbon::parse($currency->deleted_at)->format('Y-m-d H:i:s');
        return $currency;
      }
    }

    return false;
  }

  public function restore(int $id){
    $currency = $this->model->withTrashed()->find($id);
    if($currency != null && $currency->trashed()){
      $currency->is_active = 1;
      $currency->save();
      $result = $currency->restore();
      if($result){
        return $currency;
      }
    }

    return false;
  }

}


?>