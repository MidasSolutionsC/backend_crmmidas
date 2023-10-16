<?php

namespace App\Services\Implementation;

use App\Models\User;
use App\Services\Interfaces\IUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService implements IUser {

  private $model;

  public function __construct() {
    $this->model = new User();
  }
  
  public function index($data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda

    $query = User::query();
    $query->with(['person:id,nacionalidad', 'person.identifications']);
    // $query->with(['person' => function ($subQuery) {
    //   $subQuery->select(
    //     'personas.id', 
    //     'personas.nacionalidad', 
    //   ); // Lista de columnas que deseas seleccionar

    // },
    // 'person.identificationDocument' => function ($subQuery) {
    //   $subQuery->select(
    //     'documentos_identificaciones.id', 
    //     'documentos_identificaciones.personas_id', 
    //     'documentos_identificaciones.tipo_documentos_id', 
    //     'documentos_identificaciones.documento', 
    //     'documentos_identificaciones.reverso_documento',
    //     'TD.abreviacion as tipo_documentos_abreviacion'
    //   ); // Lista de columnas que deseas seleccionar
  
    //   $subQuery->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', 'TD.id');
    // }]);

  
    $query->select(
      'usuarios.*', 
      'PR.nombres as nombres', 
      'PR.apellido_paterno as apellido_paterno', 
      'PR.apellido_materno as apellido_materno', 
      'PR.nacionalidad as nacionalidad', 
      'PA.id as paises_id', 
      'PA.nombre as paises_nombre', 
      'TU.nombre as tipo_usuarios_nombre',
      // 'documentos_identificaciones.* as documentos',
      // 'PR.documento as documento', 
      // 'TD.id as tipo_documentos_id',
      // 'TD.abreviacion as tipo_documentos_abreviacion',
    );
    
    $query->join('personas as PR', 'usuarios.personas_id', 'PR.id');
    $query->join('paises as PA', 'PR.paises_id', '=', 'PA.id');
    $query->join('tipo_usuarios as TU', 'usuarios.tipo_usuarios_id', '=', 'TU.id');
    // $query->join('tipo_documentos as TD', 'PR.tipo_documentos_id', '=', 'TD.id');


    // Aplicar filtro de búsqueda si se proporciona un término
    $query->where(function ($query) use ($search) {
      if(!empty($search)){
        $query->where('nombre_usuario', 'LIKE', "%$search%")
        ->orWhere('PR.nombres', 'LIKE', "%$search%")
        ->orWhere('PR.apellido_paterno', 'LIKE', "%$search%")
        ->orWhere('PR.apellido_materno', 'LIKE', "%$search%")

        ->orWhere('PA.nombre', 'LIKE', "%$search%")
        ->orWhere('TU.nombre', 'LIKE', "%$search%");

        $query->orWhereHas('person.identificationDocument', function ($query) use ($search) {
            $query->select();
            $query->where('documento', 'like', '%' . $search . '%');
            $query->orWhereHas('typeDocument', function ($subQuery) use ($search) {
              $subQuery->select();
              $subQuery->where('abreviacion', 'LIKE', "%$search%");
            });    
        });   
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
        $item->identificaciones = $item->person->identifications;
        unset($item->person);
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
        'usuarios.*', 
        'PR.nombres as nombres', 
        'PR.apellido_paterno as apellido_paterno', 
        'PR.apellido_materno as apellido_materno', 
        // 'PR.documento as documento', 
        'PA.id as paises_id', 
        'PA.nombre as paises_nombre', 
        'TU.nombre as tipo_usuarios_nombre',
        // 'TD.id as tipo_documentos_id',
        // 'TD.abreviacion as tipo_documentos_abreviacion',
      )
      ->join('personas as PR', 'usuarios.personas_id', '=', 'PR.id')
      ->join('paises as PA', 'PR.paises_id', '=', 'PA.id')
      // ->join('tipo_documentos as TD', 'PR.tipo_documentos_id', '=', 'TD.id')
      ->join('tipo_usuarios as TU', 'usuarios.tipo_usuarios_id', '=', 'TU.id');// Usamos leftJoin para obtener usuarios incluso si no tienen documentos asociados


    $result = $query->get();
    return $result;
  }
  
  public function getAllServerSide($data){
    $query = User::query();

    $query->select(
      'usuarios.*',
      'PR.nombres as personas_nombres',
      'PR.apellido_paterno as personas_apellido_paterno',
      'PR.apellido_materno as personas_apellido_materno',
    );
    
    $query->join('personas as PR', 'usuarios.personas_id', 'PR.id');

    $data = json_decode($data, true);

    // Handle search query
    if (!empty($data['search'])) {
        $search = $data['search'];
        $query->where(function ($query) use ($search) {
            $query->where('nombre_usuario', 'like', "%$search%")
              ->orWhere('PR.nombres', 'like', "%$search%");
              // ->orWhere('clave', 'like', "%$search%")
        });
    }

    // Handle sorting
    if (!empty($data['column']) && !empty($data['order'])) {
        $column = $data['column'];
        $order = $data['order'];
        $query->orderBy($column, $order);
    }

    // Handle pagination
    $offset = !empty($data['offset'])? $data['offset'] : 0;
    $limit = !empty($data['limit'])? $data['limit'] : 10;
    $total = $query->count();
    $data = $query->skip($offset)->take($limit)->get();


    return [
      'draw' => !empty($data['draw']) ? $data['draw'] : 1,
      'recordsTotal' => $total,
      'recordsFiltered' => $total,
      'data' => $data,
    ];
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
    // $data['clave'] = password_hash($data['clave'], PASSWORD_BCRYPT);
    $data['clave'] = Hash::make($data['clave']);
    $usuario = $this->model->create($data);
    $tokenAuth = JWTAuth::fromUser($usuario);
    $usuario->save(); 
    $usuario->tipo_usuarios_nombre = $usuario->typeUser->nombre;
    return ['data' => $usuario, 'token_auth' => $tokenAuth];
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $usuario = $this->model->find($id);
    if($usuario){
      $data['clave'] = Hash::make($data['clave']);
      $usuario->fill($data);
      $usuario->save();
      $usuario->tipo_usuarios_nombre = $usuario->typeUser->nombre;
      unset($usuario->typeUser);
      return $usuario;
    } 
    
    return null;
  }

  public function delete(int $id){
    $usuario = $this->model->find($id);
    if(!is_null($usuario)){
      $usuario->is_active = 0;
      $usuario->save();
      $result = $usuario->delete();
      if($result){
        $usuario->deleted_st = Carbon::parse($usuario->deleted_at)->format('Y-m-d H:i:s');
        return $usuario;
      }
    } 
    
    return false;
  }

  public function restore(int $id){
    $usuario = $this->model->withTrashed()->find($id);
    if(!is_null($usuario) && $usuario->trashed()){
      $usuario->is_active = 1;
      $usuario->save();
      $result = $usuario->restore();
      if($result){
        return $usuario;
      }
    } 
    
    return false;
  }
  
}

?>