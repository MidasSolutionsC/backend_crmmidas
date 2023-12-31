<?php

namespace App\Services\Implementation;

use App\Models\Sale;
use App\Models\TypeUser;
use App\Models\User;
use App\Services\Interfaces\ISale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleService implements ISale
{

  private $model;

  public function __construct()
  {
    $this->model = new Sale();
  }

  public function index(array $data)
  {
    $id_usuario = Auth::user()->id;
    $o_usuario = User::find($id_usuario);
    $o_tipo_usuario = TypeUser::find($o_usuario->tipo_usuarios_id);
    $tipo_usuario = strtoupper(trim($o_tipo_usuario->nombre));

    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search'] : ""; // Término de búsqueda

    $query = $this->model->query();

    $query->with([
      'client.person.identifications',
      // 'client.person.contacts',
      // 'client.person.addresses',
      'client.company.identifications',
      // 'client.company.contacts',
      // 'client.company.addresses',
      // 'client.bankAccounts',
      // 'client.bankAccounts.typeBankAccount:id,nombre',
      // 'saleDetails',
      'userCreate.person:id,nombres,apellido_paterno,apellido_materno',
      'userCreate.typeUser:id,nombre',
      'userUpdate.person:id,nombres,apellido_paterno,apellido_materno',
      'userUpdate.typeUser:id,nombre'
    ]);
    $query->select();


    //POR ROL

    switch ($tipo_usuario) {

      case 'VENDEDOR':
        $query->where('user_create_id', $id_usuario);

        break;
      case 'BACKOFFICE':
        DB::statement("SET SQL_MODE=''");
        $query->join('integrantes as I', 'ventas.user_create_id', '=', 'I.usuarios_id')->where('I.deleted_at', null);
        $query->select([
          'ventas.*',
          'I.id as integrante_id' // Alias para la columna 'id' de la tabla 'integrantes'
        ]);
        $query->whereIn('I.grupos_id', function ($subquery) use ($id_usuario) {
          $subquery->select('grupos_id')
            ->from('integrantes')
            ->where('deleted_at', null)
            ->where('usuarios_id', $id_usuario);
        });
        $query->groupBy('ventas.id');
        break;
      case 'COORDINADOR':
        DB::statement("SET SQL_MODE=''");
        $query->join('integrantes as I', 'ventas.user_create_id', '=', 'I.usuarios_id')->where('I.deleted_at', null);
        $query->select([
          'ventas.*',
          'I.id as integrante_id' // Alias para la columna 'id' de la tabla 'integrantes'
        ]);
        $query->whereIn('I.grupos_id', function ($subquery) use ($id_usuario) {
          $subquery->select('grupos_id')
            ->from('integrantes')
            ->where('deleted_at', null)
            ->where('usuarios_id', $id_usuario);
        });
        $query->groupBy('ventas.id');
        break;

      default;
    }

    //////////


    // Aplicar filtro de búsqueda si se proporciona un término
    $query->where(function ($query) use ($search) {
      if (!empty($search)) {
        $query->where('fecha', 'LIKE', "%$search%")
          ->orWhere('hora', 'LIKE', "%$search%");

        $query->orWhereHas('client.person.identificationDocument', function ($query) use ($search) {
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
      $index = ($result->currentPage() - 1) * $result->perPage() + $key + 1;
      $item['index'] = $index;
      return $item;
    });

    $paginator = new LengthAwarePaginator($items, $result->total(), $result->perPage(), $result->currentPage());
    return $paginator;
  }

  public function getAll()
  {
    // $id_usuario = Auth::user()->id;
    // $o_usuario = User::find($id_usuario);
    // $o_tipo_usuario = TypeUser::find($o_usuario->tipo_usuarios_id);
    // $tipo_usuario = strtoupper(trim($o_tipo_usuario->nombre));


    $query = $this->model->query();
    // $query->with(['client.user.person.identifications']);
    $query->with([
      // 'client.person.identifications:documentos_identificaciones.id,documentos_identificaciones.documento',
      'client.person.identifications',
      // 'client.person.contacts',
      // 'client.person.addresses',
      'client.company.identifications',
      // 'client.company.contacts',
      // 'client.company.addresses',
      // 'client.bankAccounts:id,cuenta,tipo_cuentas_bancarias_id,is_primary',
      // 'client.bankAccounts.typeBankAccount:id,nombre',
      // 'saleDetails',
      'typeStatus',
      'userCreate.person:id,nombres,apellido_paterno,apellido_materno',
      'userCreate.typeUser:id,nombre',
      'userUpdate.person:id,nombres,apellido_paterno,apellido_materno',
      'userUpdate.typeUser:id,nombre'
    ]);
    $query->select();
    // $query->select(
    //   'ventas.*',
    //   'CL.persona_juridica as clientes_persona_juridica',
    //   DB::raw('
    //     CASE 
    //       WHEN CL.persona_juridica = 0 THEN CONCAT(PR.nombres, " ", PR.apellido_paterno, " ", PR.apellido_materno)
    //       WHEN CL.persona_juridica = 1 THEN EM.razon_social
    //       ELSE NULL
    //     END AS clientes_nombre,
    //     CASE 
    //       WHEN CL.persona_juridica = 0 THEN TDP.abreviacion
    //       WHEN CL.persona_juridica = 1 THEN TDE.abreviacion
    //       ELSE NULL
    //     END AS clientes_tipo_documento,
    //     CASE 
    //       WHEN CL.persona_juridica = 0 THEN PR.documento
    //       WHEN CL.persona_juridica = 1 THEN EM.documento
    //       ELSE NULL
    //     END AS clientes_documento
    //   ')
    // );

    // $query->leftJoin('clientes as CL', 'ventas.clientes_id', 'CL.id');
    // $query->leftJoin('personas as PR', 'CL.personas_id', 'PR.id');
    // $query->leftJoin('empresas as EM', 'CL.empresas_id', 'EM.id');
    // // $query->leftJoin('tipo_documentos as TDP', 'PR.tipo_documentos_id', 'TDP.id');
    // $query->leftJoin('tipo_documentos as TDE', 'EM.tipo_documentos_id', 'TDE.id');

    // switch ($tipo_usuario) {

    //   case 'VENDEDOR':
    //     $query->where('user_create_id', $id_usuario);

    //     break;
    //   case 'BACKOFFICE':
    //     DB::statement("SET SQL_MODE=''");
    //     $query->join('integrantes as I', 'ventas.user_create_id', '=', 'I.usuarios_id');
    //     $query->whereIn('I.grupos_id', function ($subquery) use ($id_usuario) {
    //       $subquery->select('grupos_id')
    //         ->from('integrantes')
    //         ->where('usuarios_id', $id_usuario);
    //     });
    //     $query->groupBy('ventas.id');
    //     break;

    //   default;
    // }

    $result = $query->get();
    return $result;
  }

  public function getById(int $id)
  {
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function getByIdWithAll(int $id)
  {
    $query = $this->model->query();
    // $query->with(['client.user.person.identifications']);
    $query->with([
      // 'client.person.identifications:documentos_identificaciones.id,documentos_identificaciones.documento',
      'client.person.identifications',
      'client.person.contacts',
      'client.person.addresses',
      'client.company.identifications',
      'client.company.contacts',
      'client.company.addresses',
      // 'client.bankAccounts:id,cuenta,tipo_cuentas_bancarias_id,is_primary',
      'client.bankAccounts.typeBankAccount:id,nombre',
      'saleDetails',
      'installations',
      'userCreate.person:id,nombres,apellido_paterno,apellido_materno',
      'userCreate.typeUser:id,nombre',
      'userUpdate.person:id,nombres,apellido_paterno,apellido_materno',
      'userUpdate.typeUser:id,nombre'
    ]);
    $query->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data)
  {
    $data['created_at'] = Carbon::now();
    if (isset($data['user_auth_id'])) {
      $data['user_create_id'] = $data['user_auth_id'];
    }
    $sale = $this->model->create($data);
    if ($sale) {
      $sale->created_at = Carbon::parse($sale->created_at)->format('Y-m-d H:i:s');
    }

    return $sale;
  }

  public function update(array $data, int $id)
  {
    $data['updated_at'] = Carbon::now();
    if (isset($data['user_auth_id'])) {
      $data['user_update_id'] = $data['user_auth_id'];
    }
    $sale = $this->model->find($id);
    if ($sale) {
      $sale->fill($data);
      $sale->save();
      $sale->updated_at = Carbon::parse($sale->updated_at)->format('Y-m-d H:i:s');
      return $sale;
    }

    return null;
  }

  public function delete(int $id)
  {
    $sale = $this->model->find($id);
    if ($sale != null) {
      $sale->is_active = 0;
      $sale->save();
      $result = $sale->delete();
      if ($result) {
        $sale->deleted_st = Carbon::parse($sale->deleted_at)->format('Y-m-d H:i:s');
        return $sale;
      }
    }

    return false;
  }

  public function restore(int $id)
  {
    $sale = $this->model->withTrashed()->find($id);
    if ($sale != null && $sale->trashed()) {
      $sale->is_active = 1;
      $sale->save();
      $result = $sale->restore();
      if ($result) {
        return $sale;
      }
    }

    return false;
  }
}
