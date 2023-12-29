<?php 

namespace App\Services\Implementation;

use App\Models\TypeBankAccount;
use App\Services\Interfaces\ITypeBankAccount;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class TypeBankAccountService implements ITypeBankAccount{
  
  private $model;

  public function __construct() {
    $this->model = new TypeBankAccount();
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
          ->orWhere('abreviacion', 'like', "%$search%")
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
    $existingRecord = $this->model->withTrashed()->where('nombre', $data['nombre'])->whereNotNull('deleted_at')->first();
    $typeBankAccount = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      $existingRecord->updated_at = Carbon::now(); 
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if($result){
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $typeBankAccount = $existingRecord;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $data['created_at'] = Carbon::now(); 
      $data['user_create_id'] = $data['user_auth_id'];
      $typeBankAccount = $this->model->create($data);
      if($typeBankAccount){
        $typeBankAccount->created_at = Carbon::parse($typeBankAccount->created_at)->format('Y-m-d H:i:s');
      }
    }
    
    return $typeBankAccount;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $typeBankAccount = $this->model->find($id);
    if($typeBankAccount){
      $typeBankAccount->fill($data);
      $typeBankAccount->save();
      $typeBankAccount->updated_at = Carbon::parse($typeBankAccount->updated_at)->format('Y-m-d H:i:s');
      return $typeBankAccount;
    }

    return null;
  }

  public function delete(int $id){
    $typeBankAccount = $this->model->find($id);
    if($typeBankAccount != null){
      $typeBankAccount->is_active = 0;
      $typeBankAccount->save();
      $result = $typeBankAccount->delete();
      if($result){
        $typeBankAccount->deleted_st = Carbon::parse($typeBankAccount->deleted_at)->format('Y-m-d H:i:s');
        return $typeBankAccount;
      }
    }

    return false;
  }

  public function restore(int $id){
    $typeBankAccount = $this->model->withTrashed()->find($id);
    if($typeBankAccount != null && $typeBankAccount->trashed()){
      $typeBankAccount->is_active = 1;
      $typeBankAccount->save();
      $result = $typeBankAccount->restore();
      if($result){
        return $typeBankAccount;
      }
    }

    return false;
  }

}


?>