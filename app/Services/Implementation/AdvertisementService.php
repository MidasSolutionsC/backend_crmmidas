<?php

namespace App\Services\Implementation;

use App\Models\Advertisement;
use App\Services\Interfaces\IAdvertisement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdvertisementService implements IAdvertisement
{

  private $model;

  public function __construct()
  {
    $this->model = new Advertisement();
  }

  public function index(array $data)
  {
    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search'] : ""; // Término de búsqueda

    $query = Advertisement::query()->orderBy('order', 'asc');
    $query->selectRaw(
      '*,      
      CASE 
        WHEN tipo = "I" THEN "Interno" 
        WHEN tipo = "E" THEN "Externo" 
      ELSE "" END AS tipo_text'
    );


    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
      $query->where('titulo', 'LIKE', "%$search%")
        ->orWhere('descripcion', 'LIKE', "%$search%")
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

  public function getAll()
  {
    $query = $this->model->selectRaw(
      '*, 
      CASE 
        WHEN tipo = "I" THEN "Interno" 
        WHEN tipo = "E" THEN "Externo" 
      ELSE "" END AS tipo_text'
    );
    $result = $query->orderBy('order', 'asc')->get();
    return $result;
  }

  public function getById(int $id)
  {
    $query = $this->model->select();
    $advertisement = $query->find($id);
    switch ($advertisement->tipo) {
      case 'I':
        $advertisement->tipo_text = 'Interno';
        break;
      case 'E':
        $advertisement->tipo_text = 'Externo';
        break;
      default:
        $advertisement->tipo_text = '';
        break;
    }
    return $advertisement;
  }

  public function create(array $data)
  {
    $data['created_at'] = Carbon::now();
    $data['user_create_id'] = $data['user_auth_id'];
    $advertisement = $this->model->create($data);
    if ($advertisement) {
      $advertisement->created_at = Carbon::parse($advertisement->created_at)->format('Y-m-d H:i:s');
      switch ($advertisement->tipo) {
        case 'I':
          $advertisement->tipo_text = 'Interno';
          break;
        case 'E':
          $advertisement->tipo_text = 'Externo';
          break;
        default:
          $advertisement->tipo_text = '';
          break;
      }
    }

    $this->orderAll();

    return $advertisement;
  }

  public function update(array $data, int $id)
  {
    $data['updated_at'] = Carbon::now();
    $data['user_update_id'] = $data['user_auth_id'];
    $advertisement = $this->model->find($id);
    if ($advertisement) {
      $advertisement->fill($data);
      $advertisement->save();
      $advertisement->updated_at = Carbon::parse($advertisement->updated_at)->format('Y-m-d H:i:s');
      switch ($advertisement->tipo) {
        case 'I':
          $advertisement->tipo_text = 'Interno';
          break;
        case 'E':
          $advertisement->tipo_text = 'Externo';
          break;
        default:
          $advertisement->tipo_text = '';
          break;
      }
      $this->orderAll();

      return $advertisement;
    }

    return null;
  }

  public function order(array $data)
  {


    foreach ($data as $orden => $id) {

      $advertisement = $this->model->find($id);
      $advertisement->order = $orden + 1;
      $advertisement->save();
    }

    return null;
  }

  private function orderAll()
  {
    $data_internal = $this->model->where('tipo', 'I')->where('deleted_at', NULL)->orderBy('order', 'asc')->get();

    foreach ($data_internal as $orden => $advertisement) {
      $advertisement->order = $orden + 1;
      $advertisement->save();
    }

    $data_external = $this->model->where('tipo', 'E')->where('deleted_at', NULL)->orderBy('order', 'asc')->get();

    foreach ($data_external as $orden => $advertisement) {
      $advertisement->order = $orden + 1;
      $advertisement->save();
    }
    return null;
  }

  public function delete(int $id)
  {
    $advertisement = $this->model->find($id);
    if ($advertisement != null) {
      $advertisement->save();
      $result = $advertisement->delete();
      if ($result) {
        $advertisement->deleted_st = Carbon::parse($advertisement->deleted_at)->format('Y-m-d H:i:s');
        $this->orderAll();
        return $advertisement;
      }
    }


    return false;
  }

  public function restore(int $id)
  {
    $advertisement = $this->model->withTrashed()->find($id);
    if ($advertisement != null && $advertisement->trashed()) {
      $advertisement->save();
      $result = $advertisement->restore();
      if ($result) {
        switch ($advertisement->tipo) {
          case 'I':
            $advertisement->tipo_text = 'Interno';
            break;
          case 'E':
            $advertisement->tipo_text = 'Externo';
            break;
          default:
            $advertisement->tipo_text = '';
            break;
        }

        $this->orderAll();

        return $advertisement;
      }
    }



    return false;
  }
}
