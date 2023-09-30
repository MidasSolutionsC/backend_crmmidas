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
      'PP.precio as precio',
      'TM.id as tipo_monedas_id',
      'TM.nombre as tipo_monedas_nombre',
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
    $query->join('tipo_monedas as TM', 'PP.tipo_monedas_id', '=', 'TM.id');

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

  public function search(array $data){
    $search = $data['search'];
    $typeServiceId = !empty($data['tipo_servicios_id'])? $data['tipo_servicios_id']: null;

    $query = $this->model->query();
    if(!is_null($typeServiceId)){
      $query->where("tipo_servicios_id", $typeServiceId);
    }

    $query->whereRaw("nombre like ?", ['%' . $search . '%']);
    $query->orderBy('id', 'desc');
    $query->take(20);
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
    $product = null;

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
        $product = $existingRecord;
        $product->tipo_servicios_nombre = $product->typeService->nombre;
        $product->precio = $product->getLastPrice(); 
      }
    } else {
      $data['created_at'] = Carbon::now(); 
      if(isset($data['user_auth_id'])){
        $data['user_create_id'] = $data['user_auth_id'];
      }
      $product = $this->model->create($data);
      if($product){
        $product->tipo_servicios_nombre = $product->typeService->nombre;
        $product->precio = $product->getLastPrice(); 
      }
    }
    
    return $product;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id']; 
    }

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