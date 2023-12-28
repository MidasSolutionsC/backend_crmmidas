<?php

namespace App\Services\Implementation;

use App\Models\Country;
use App\Services\Interfaces\ICountry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class CountryService implements ICountry{

  private $model;

  public function __construct()
  {
    $this->model = new Country();
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
          ->orWhere('iso_code', 'like', "%$search%")
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

  public function create(array $data){
    $existingRecord = $this->model->withTrashed()
      ->where('nombre', $data['nombre'])
      ->where('iso_code', $data['iso_code'])
      ->whereNotNull('deleted_at')->first();
      
    $country = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $country = $existingRecord;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $data['created_at'] = Carbon::now(); 
      $country = $this->model->create($data);
      if($country){
        $country->created_at = Carbon::parse($country->created_at)->format('Y-m-d H:i:s');
      }
    }
    
    return $country;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now();
    $country = $this->model->find($id);
    if($country){
      $country->fill($data);
      $country->save();
      $country->updated_at = Carbon::parse($country->updated_at)->format('Y-m-d H:i:s');
      return $country;
    }

    return null;
  }

  public function delete(int $id){
    $country = $this->model->find($id);
    if($country != null){
      $country->save();
      $result = $country->delete();
      if($result){
        $country->deleted_st = Carbon::parse($country->deleted_at)->format('Y-m-d H:i:s');
        return $country;
      }
    }

    return false;
  }

  public function restore(int $id){
    $country = $this->model->withTrashed()->find($id);
    if($country != null && $country->trashed()){
      $country->save();
      $result = $country->restore();
      if($result){
        return $country;
      }
    }

    return false;
  }

}

?>