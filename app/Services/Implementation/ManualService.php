<?php 

namespace App\Services\Implementation;

use App\Models\Manual;
use App\Services\Interfaces\IManual;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ManualService implements IManual{
  
  private $model;

  public function __construct() {
    $this->model = new Manual();
  }


  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = Manual::query();
    $query->selectRaw(
      '*,      
      CASE 
        WHEN tipo = "S" THEN "Manual de Software" 
        WHEN tipo = "B" THEN "Gestión de Backlog" 
        WHEN tipo = "M" THEN "Vodafone Micropyme"
        WHEN tipo = "R" THEN "Vodafone Residencial"
        WHEN tipo = "O" THEN "Otro"
      ELSE "" END AS tipo_text'
    );
    

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('nombre', 'LIKE', "%$search%")
              ->orWhere('tipo', 'LIKE', "%$search%");
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
    $query = $this->model->selectRaw('*, 
      CASE 
        WHEN tipo = "S" THEN "Manual de Software" 
        WHEN tipo = "B" THEN "Gestión de Backlog" 
        WHEN tipo = "M" THEN "Vodafone Micropyme"
        WHEN tipo = "R" THEN "Vodafone Residencial"
        WHEN tipo = "O" THEN "Otro"
      ELSE "" END AS tipo_text'
    );

    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $manual = $query->find($id);
    switch ($manual->tipo) {
      case 'S':
        $manual->tipo_text = 'Manual de Software';
        break;
      case 'B':
        $manual->tipo_text = 'Gestión de Backlog';
        break;
      case 'M':
        $manual->tipo_text = 'Vodafone Micropyme';
        break;
      case 'R':
        $manual->tipo_text = 'Vodafone Residencial';
        break;
      case 'O':
        $manual->tipo_text = 'Otro';
        break;
      default:
        $manual->tipo_text = '';
        break;
    }
    return $manual;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    $data['user_create_id'] = $data['user_auth_id']; 
    $manual = $this->model->create($data);
    if($manual){
      $manual->created_at = Carbon::parse($manual->created_at)->format('Y-m-d H:i:s');
      switch ($manual->tipo) {
        case 'S':
          $manual->tipo_text = 'Manual de Software';
          break;
        case 'B':
          $manual->tipo_text = 'Gestión de Backlog';
          break;
        case 'M':
          $manual->tipo_text = 'Vodafone Micropyme';
          break;
        case 'R':
          $manual->tipo_text = 'Vodafone Residencial';
          break;
        case 'O':
          $manual->tipo_text = 'Otro';
          break;
        default:
          $manual->tipo_text = '';
          break;
      }
    }

    return $manual;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id']; 
    $manual = $this->model->find($id);
    if($manual){
      $manual->fill($data);
      $manual->save();
      $manual->updated_at = Carbon::parse($manual->updated_at)->format('Y-m-d H:i:s');
      switch ($manual->tipo) {
        case 'S':
          $manual->tipo_text = 'Manual de Software';
          break;
        case 'B':
          $manual->tipo_text = 'Gestión de Backlog';
          break;
        case 'M':
          $manual->tipo_text = 'Vodafone Micropyme';
          break;
        case 'R':
          $manual->tipo_text = 'Vodafone Residencial';
          break;
        case 'O':
          $manual->tipo_text = 'Otro';
          break;
        default:
          $manual->tipo_text = '';
          break;
      }
      return $manual;
    }

    return null;
  }

  public function delete(int $id){
    $manual = $this->model->find($id);
    if($manual != null){
      $manual->save();
      $result = $manual->delete();
      if($result){
        $manual->deleted_st = Carbon::parse($manual->deleted_at)->format('Y-m-d H:i:s');
        return $manual;
      }
    }

    return false;
  }

  public function restore(int $id){
    $manual = $this->model->withTrashed()->find($id);
    if($manual != null && $manual->trashed()){
      $manual->save();
      $result = $manual->restore();
      if($result){
        switch ($manual->tipo) {
          case 'S':
            $manual->tipo_text = 'Manual de Software';
            break;
          case 'B':
            $manual->tipo_text = 'Gestión de Backlog';
            break;
          case 'M':
            $manual->tipo_text = 'Vodafone Micropyme';
            break;
          case 'R':
            $manual->tipo_text = 'Vodafone Residencial';
            break;
          case 'O':
            $manual->tipo_text = 'Otro';
            break;
          default:
            $manual->tipo_text = '';
            break;
        }
        return $manual;
      }
    }

    return false;
  }

}


?>