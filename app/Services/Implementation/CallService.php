<?php

namespace App\Services\Implementation;

use App\Models\Call;
use App\Services\Interfaces\ICall;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;



class CallService implements ICall{

  private $model;

  public function __construct()
  {
    $this->model = new Call();
  }

  
  public function index(array $data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = Call::query();
    $query->select(
      'llamadas.*', 
      'TE.nombre as tipo_estados_nombre',
      'OP.nombre as operadores_nombre',
      'OL.nombre as operadores_llamo_nombre',
      'TP.nombre as tipificaciones_llamadas_nombre',
    );
    
    $query->join('tipo_estados as TE', 'llamadas.tipo_estados_id', '=', 'TE.id');
    $query->leftJoin('operadores as OP', 'llamadas.operadores_id', '=', 'OP.id');
    $query->leftJoin('operadores as OL', 'llamadas.operadores_llamo_id', '=', 'OL.id');
    $query->leftJoin('tipificaciones_llamadas as TP', 'llamadas.tipificaciones_llamadas_id', '=', 'TP.id');

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('numero', 'LIKE', "%$search%")
              ->orWhere('OP.nombre', 'LIKE', "%$search%")
              ->orWhere('OL.nombre', 'LIKE', "%$search%")
              ->orWhere('TP.nombre', 'LIKE', "%$search%")
              ->orWhere('nombres', 'LIKE', "%$search%")
              ->orWhere('apellido_paterno', 'LIKE', "%$search%")
              ->orWhere('apellido_materno', 'LIKE', "%$search%")
              ->orWhere('direccion', 'LIKE', "%$search%")
              ->orWhere('permanencia', 'LIKE', "%$search%")
              ->orWhere('permanencia_tiempo', 'LIKE', "%$search%")
              ->orWhere('fecha', 'LIKE', "%$search%")
              ->orWhere('hora', 'LIKE', "%$search%")
              ->orWhere('TE.nombre', 'LIKE', "%$search%");
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
      'llamadas.*', 
      'TE.nombre as tipo_estados_nombre',
      'OP.nombre as operadores_nombre',
      'OL.nombre as operadores_llamo_nombre',
      'TP.nombre as tipificaciones_llamadas_nombre',
    );
    
    $query->join('tipo_estados as TE', 'llamadas.tipo_estados_id', '=', 'TE.id');
    $query->leftJoin('operadores as OP', 'llamadas.operadores_id', '=', 'OP.id');
    $query->leftJoin('operadores as OL', 'llamadas.operadores_llamo_id', '=', 'OL.id');
    $query->leftJoin('tipificaciones_llamadas as TP', 'llamadas.tipificaciones_llamadas_id', '=', 'TP.id');
    $result = $query->get();
    return $result;
  }

  public function getFilterByUser(int $userId){
    $query = $this->model->select();
    if($userId){
      $query->where('user_create_id', $userId);
    }
    
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
    $call = $this->model->create($data);
    if($call){
      $call->created_at = Carbon::parse($call->created_at)->format('Y-m-d H:i:s');
    }

    return $call;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $data['user_update_id'] = $data['user_auth_id'];
    $call = $this->model->find($id);
    if($call){
      $call->fill($data);
      $call->save();
      $call->updated_at = Carbon::parse($call->updated_at)->format('Y-m-d H:i:s');
      return $call;
    }

    return null;
  }

  public function delete(int $id){
    $call = $this->model->find($id);
    if($call != null){
      $call->is_active = 0;
      $call->save();
      $result = $call->delete();
      if($result){
        $call->deleted_st = Carbon::parse($call->deleted_at)->format('Y-m-d H:i:s');
        return $call;
      }
    }

    return false;
  }

  public function restore(int $id){
    $call = $this->model->withTrashed()->find($id);
    if($call != null && $call->trashed()){
      $call->is_active = 1;
      $call->save();
      $result = $call->restore();
      if($result){
        return $call;
      }
    }

    return false;
  }

}


?>