<?php

namespace App\Services\Implementation;

use App\Models\Product;
use App\Services\Interfaces\IProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService implements IProduct{

  private $model;

  public function __construct()
  {
    $this->model = new Product();
  }

  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = Product::query();
    $query->select(
      'productos.*', 
      'TS.nombre as tipo_servicios_nombre', 
      'PP.precio as precio'
    );
    
    $query->join('tipo_servicios as TS', 'productos.tipo_servicios_id', '=', 'TS.id');
    $query->leftJoin('productos_precios as PP', function ($join) {
      $join->on('productos.id', '=', 'PP.productos_id')
        ->where('PP.created_at', '=', function ($subQuery) {
            $subQuery->select(DB::raw('MAX(created_at)'))
                ->from('productos_precios')
                ->whereColumn('productos_id', 'productos.id');
        });
    });

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('productos.nombre', 'LIKE', "%$search%")
              ->orWhere('productos.descripcion', 'LIKE', "%$search%")
              ->orWhere('PP.precio', 'LIKE', "%$search%")
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
      'productos.*', 
      'tipo_servicios.nombre as tipo_servicios_nombre', 
      'pp.precio as precio'
      )
      ->join('tipo_servicios', 'productos.tipo_servicios_id', '=', 'tipo_servicios.id')
      ->leftJoin('productos_precios as pp', function ($join) {
          $join->on('productos.id', '=', 'pp.productos_id')
              ->where('pp.created_at', '=', function ($subquery) {
                  $subquery->select(DB::raw('MAX(created_at)'))
                      ->from('productos_precios')
                      ->whereColumn('productos_id', 'productos.id');
              });
      });
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
    $data['user_create_id'] = $data['user_auth_id']; 
    $product = $this->model->create($data);
    if($product){
      $product->created_at = Carbon::parse($product->created_at)->format('Y-m-d H:i:s');
    }

    return $product;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id']; 
    $product = $this->model->find($id);
    if($product){
      $product->fill($data);
      $product->save();
      $product->updated_at = Carbon::parse($product->updated_at)->format('Y-m-d H:i:s');
      return $product;
    }

    return null;
  }

  public function delete(int $id){
    $product = $this->model->find($id);
    if($product != null){
      $product->is_active = 0;
      $product->save();
      $result = $product->delete();
      if($result){
        $product->deleted_st = Carbon::parse($product->deleted_at)->format('Y-m-d H:i:s');
        return $product;
      }
    }

    return false;
  }

  public function restore(int $id){
    $product = $this->model->withTrashed()->find($id);
    if($product != null && $product->trashed()){
      $product->is_active = 1;
      $product->save();
      $result = $product->restore();
      if($result){
        return $product;
      }
    }

    return false;
  }

}


?>