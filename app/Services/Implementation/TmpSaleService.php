<?php

namespace App\Services\Implementation;

use App\Models\TmpSale;
use App\Services\Interfaces\ISale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TmpSaleService implements ISale{

  private $model;

  public function __construct()
  {
    $this->model = new TmpSale();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = $this->model->query();

    $query->select();

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        // $query->where('productos.nombre', 'LIKE', "%$search%")
        //       ->orWhere('productos.descripcion', 'LIKE', "%$search%")
        //       ->orWhere('PP.precio', 'LIKE', "%$search%")
        //       ->orWhere('TS.nombre', 'LIKE', "%$search%");
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

    $sale = $this->model->create($data);
    if($sale){
      $sale->created_at = Carbon::parse($sale->created_at)->format('Y-m-d H:i:s');
    }

    return $sale;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
 
    $sale = $this->model->find($id);
    if($sale){
      $sale->fill($data);
      $sale->save();
      $sale->updated_at = Carbon::parse($sale->updated_at)->format('Y-m-d H:i:s');
      return $sale;
    }

    return null;
  }

  public function delete(int $id){
    $sale = $this->model->find($id);
    if($sale != null){
      $sale->is_active = 0;
      $sale->save();
      $result = $sale->delete();
      if($result){
        $sale->deleted_st = Carbon::parse($sale->deleted_at)->format('Y-m-d H:i:s');
        return $sale;
      }
    }

    return false;
  }

  public function restore(int $id){
    $sale = $this->model->withTrashed()->find($id);
    if($sale != null && $sale->trashed()){
      $sale->is_active = 1;
      $sale->save();
      $result = $sale->restore();
      if($result){
        return $sale;
      }
    }

    return false;
  }

}

?>