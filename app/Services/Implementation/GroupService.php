<?php

namespace App\Services\Implementation;

use App\Models\Group;
use App\Services\Interfaces\IGroup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupService implements IGroup{

  private $model;

  public function __construct()
  {
    $this->model = new Group();
  }

  public function index($data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda
    
    $query = Group::query();
    // $query = User::with(['person', 'typeUser']); // Carga las relaciones 'persona' y 'tipoUsuario'
    $query->select(
      'grupos.*',
      'SD.nombre as sedes_nombre',
      'UC.nombre_usuario as usuarios_create_nombre_usuario',
      'UM.nombre_usuario as usuarios_update_nombre_usuario',
      'PC.nombres as personas_create_nombres',
      'PC.apellido_paterno as personas_create_apellido_paterno',
      'PC.apellido_materno as personas_create_apellido_materno',
    );
    
    $query->join('sedes as SD', 'grupos.sedes_id', 'SD.id');
    $query->join('usuarios as UC', 'grupos.user_create_id', 'UC.id');
    $query->leftJoin('usuarios as UM', 'grupos.user_update_id', 'UM.id');
    $query->join('personas as PC', 'UC.personas_id', 'PC.id');

    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
        $query->where('grupos.nombre', 'LIKE', "%$search%")
              ->orWhere('SD.nombre', 'LIKE', "%$search%");
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
      'grupos.*',
      'SD.nombre as sedes_nombre',
      'UC.nombre_usuario as usuarios_create_nombre_usuario',
      'PC.nombres as personas_create_nombres',
      'PC.apellido_paterno as personas_create_apellido_paterno',
      'PC.apellido_materno as personas_create_apellido_materno',
    )->join('sedes as SD', 'grupos.sedes_id', 'SD.id')
      ->join('usuarios as UC', 'grupos.user_create_id', 'UC.id')
      ->join('personas as PC', 'UC.personas_id', 'PC.id');

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
    $group = $this->model->create($data);
    if($group){
      $group->created_at = Carbon::parse($group->created_at)->format('Y-m-d H:i:s');
      $group->sedes_nombre = $group->campus->nombre;
    }

    return $group;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }
    $group = $this->model->find($id);
    if($group){
      $group->fill($data);
      $group->save();
      $group->updated_at = Carbon::parse($group->updated_at)->format('Y-m-d H:i:s');
      $group->sedes_nombre = $group->campus->nombre;
      return $group;
    }

    return null;
  }

  public function delete(int $id){
    $group = $this->model->find($id);
    if($group != null){
      $group->is_active = 0;
      $group->save();
      $result = $group->delete();
      if($result){
        $group->deleted_st = Carbon::parse($group->deleted_at)->format('Y-m-d H:i:s');
        return $group;
      }
    }

    return false;
  }

  public function restore(int $id){
    $group = $this->model->withTrashed()->find($id);
    if($group != null && $group->trashed()){
      $group->is_active = 1;
      $group->save();
      $result = $group->restore();
      if($result){
        return $group;
      }
    }

    return false;
  }

}


?>