<?php

namespace App\Services\Implementation;

use App\Models\Campus;
use App\Services\Interfaces\ICampus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CampusService implements ICampus
{

  private $model;

  public function __construct()
  {
    $this->model = new Campus();
  }

  public function index($data)
  {
    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search'] : ""; // Término de búsqueda

    $query = $this->model->query();
    $query->with(['country']);

    // Aplicar filtro de búsqueda si se proporciona un término
    $query->where(function ($query) use ($search) {
      if (!empty($search)) {
        $query->where('nombre', 'LIKE', "%$search%")
          ->orWhere('codigo_ubigeo', 'like', "%$search%")
          ->orWhere('ciudad', 'like', "%$search%")
          ->orWhere('direccion', 'like', "%$search%")
          ->orWhere('codigo_postal', 'like', "%$search%")
          ->orWhere('telefono', 'like', "%$search%")
          ->orWhere('correo', 'like', "%$search%")
          ->orWhere('responsable', 'like', "%$search%")
          ->orWhere('created_at', 'like', "%$search%")
          ->orWhere('updated_at', 'like', "%$search%");
      }

      $query->orWhereHas('country', function ($query) use ($search) {
        if (!empty($search)) {
          $query->where('nombre', 'LIKE', '%' . $search . '%')
            ->orWhere('iso_code', 'LIKE', "%$search%");
        }
      });
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
    $query = $this->model->select(
      'sedes.*',
      'PS.nombre as paises_nombre',
    );

    $query->selectRaw("CONCAT(UB.dpto, ', ', UB.prov, ', ', UB.distrito) as ubigeos_ciudad");
    $query->join('paises as PS', 'sedes.paises_id', '=', 'PS.id');
    $query->leftJoin('ubigeos as UB', 'sedes.codigo_ubigeo', '=', 'UB.ubigeo');
    $result = $query->get();
    return $result;
  }

  public function getById(int $id)
  {
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data)
  {
    $existingRecord = $this->model->withTrashed()->where('nombre', $data['nombre'])->whereNotNull('deleted_at')->first();
    $campus = null;

    if (!is_null($existingRecord) && $existingRecord->trashed()) {
      if (isset($data['user_auth_id'])) {
        $existingRecord->user_update_id = $data['user_auth_id'];
      }
      $existingRecord->updated_at = Carbon::now();
      $existingRecord->is_active = 1;
      $existingRecord->save();
      $result = $existingRecord->restore();
      if ($result) {
        $existingRecord->updated_at = Carbon::parse($existingRecord->updated_at)->format('Y-m-d H:i:s');
        $campus = $existingRecord;

        $country = $campus->country;
        $ubigeo = $campus->ubigeo;

        $campus->paises_nombre = $country->nombre;
        $campus->ubigeos_ciudad = $ubigeo->dpto . " " . $ubigeo->prov . " " . $ubigeo->distrito;
      }
    } else {
      // No existe un registro con el mismo valor, puedes crear uno nuevo
      $data['created_at'] = Carbon::now();
      if (isset($data['user_auth_id'])) {
        $data['user_create_id'] = $data['user_auth_id'];
      }
      $campus = $this->model->create($data);
      if ($campus) {
        $campus->created_at = Carbon::parse($campus->created_at)->format('Y-m-d H:i:s');
        $country = $campus->country;
        $ubigeo = $campus->ubigeo;

        $campus->paises_nombre = $country->nombre;
        $campus->ubigeos_ciudad = $ubigeo->dpto . " " . $ubigeo->prov . " " . $ubigeo->distrito;
      }
    }

    return $campus;
  }

  public function update(array $data, int $id)
  {
    $data['updated_at'] = Carbon::now();
    $data['user_update_id'] = $data['user_auth_id'];
    $campus = $this->model->find($id);
    if ($campus) {
      $campus->fill($data);
      $campus->save();
      $country = $campus->country;
      $ubigeo = $campus->ubigeo;

      $campus->paises_nombre = $country->nombre;
      $campus->ubigeos_ciudad = $ubigeo->dpto . ", " . $ubigeo->prov . ", " . $ubigeo->distrito;
      return $campus;
    }

    return null;
  }

  public function delete(int $id)
  {
    $campus = $this->model->find($id);
    if ($campus != null) {
      $campus->is_active = false;
      $campus->save();
      $result = $campus->delete();
      if ($result) {
        $campus->deleted_st = Carbon::parse($campus->deleted_at)->format('Y-m-d H:i:s');
        return $campus;
      }
    }

    return false;
  }

  public function restore(int $id)
  {
    $campus = $this->model->withTrashed()->find($id);
    if ($campus != null && $campus->trashed()) {
      $campus->is_active = true;
      $campus->save();
      $result = $campus->restore();
      if ($result) {
        return $campus;
      }
    }

    return false;
  }
}
