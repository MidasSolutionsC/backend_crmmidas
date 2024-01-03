<?php

namespace App\Services\Implementation;

use App\Models\SaleComment;
use App\Services\Interfaces\ISaleComment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class SaleCommentService implements ISaleComment
{

  private $model;

  public function __construct()
  {
    $this->model = new SaleComment();
  }


  public function index(array $data)
  {
    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search'] : ""; // Término de búsqueda
    $saleDetailId = !empty($data['ventas_detalles_id']) ? $data['ventas_detalles_id'] : null; // Término de búsqueda

    $query = SaleComment::query();
    $query->select();

    if (!empty($saleDetailId)) {
      $query->where('ventas_detalles_id', $saleDetailId);
    }


    // Aplicar filtro de búsqueda si se proporciona un término
    if (!empty($search)) {
      // $query->where('IT.localidad', 'LIKE', "%$search%")
      //       ->orWhere('IT.provincia', 'LIKE', "%$search%")
      //       ->orWhere('SR.nombre', 'LIKE', "%$search%");
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
    $query = $this->model->select();
    $result = $query->get();
    return $result;
  }

  public function getFilterBySale(int $saleId)
  {
    $query = $this->model->select();
    if ($saleId) {
      $query->where('ventas_id', $saleId);
    }
    $result = $query->get();
    return $result;
  }

  public function getFilterBySaleDetail(int $saleDetailId)
  {
    $query = $this->model->query();

    $query->with(['userCreate.person', 'userCreate.typeUser:id,nombre']);
    $query->select();

    if ($saleDetailId) {
      $query->where('ventas_detalles_id', $saleDetailId);
    }

    $result = $query->orderBy('created_at', 'desc')
      ->take(10) // Obtener los últimos 10 registros
      ->get();


    $newResult = collect($result)->sortBy('created_at')->values();
    return $newResult;
  }

  public function getFilterBySaleDetailAsync(array $data)
  {
    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search'] : ""; // Término de búsqueda
    $saleDetailId = !empty($data['ventas_detalles_id']) ? $data['ventas_detalles_id'] : null; // Término de búsqueda

    $query = SaleComment::query();
    $query->select();

    if (!empty($saleDetailId)) {
      $query->where('ventas_detalles_id', $saleDetailId);
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

  public function getLastRowsPagination(array $data)
  {
    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 20; // Elementos por página
    $saleDetailId = !empty($data['ventas_detalles_id']) ? $data['ventas_detalles_id'] : null; // Término de búsqueda

    $query = SaleComment::query();

    if (!empty($saleDetailId)) {
      $query->where('ventas_detalles_id', $saleDetailId);
    }

    $totalRecords = $query->count(); // Obtener el total de registros

    // Calcular la página actual
    $currentPage = max(1, ceil($totalRecords / $perPage) - $page + 1);

    $last20Query = $query->orderBy('id', 'asc')
      ->skip(max(0, $totalRecords - $perPage)) // Saltar los registros anteriores a los últimos 20
      ->take($perPage); // Tomar los últimos 20 registros

    $result = $last20Query->paginate($perPage, ['*'], 'page', $currentPage);
    $items = new Collection($result->items());
    $items = $items->map(function ($item, $key) use ($result) {
      $index = ($result->currentPage() - 1) * $result->perPage() + $key + 1;
      $item['index'] = $index;
      return $item;
    });

    $paginator = new LengthAwarePaginator($items, $result->total(), $result->perPage(), $result->currentPage());
    return $paginator;
  }

  public function getAdjacentMessages(array $data)
  {

    $page = !empty($data['page']) ? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 20; // Elementos por página
    $saleId = !empty($data['ventas_id']) ? $data['ventas_id'] : null; // Término de búsqueda
    $saleDetailId = !empty($data['ventas_detalles_id']) ? $data['ventas_detalles_id'] : null; // Término de búsqueda
    $messageId = !empty($data['ventas_comentarios_id']) ? $data['ventas_comentarios_id'] : null; // Término de búsqueda
    $direction = !empty($data['direccion']) ? $data['direccion'] : 'next'; // Término de búsqueda

    $query = SaleComment::query();
    $query->with(['userCreate.person', 'userCreate.typeUser:id,nombre']);

    if (!empty($saleDetailId)) {
      $query->where('ventas_detalles_id', $saleDetailId);
    }

    if ($direction === 'next') {
      $query->where('id', '>', $messageId)->orderBy('id', 'asc');
    } elseif ($direction === 'previous') {
      $query->where('id', '<', $messageId)->orderBy('id', 'desc');
    } else {
      return null;
    }
    

    $result = $query->paginate($perPage);

    $items = new Collection($result->items());
    $items = collect($items)->sortBy('created_at')->values();

    $paginator = new LengthAwarePaginator($items, $result->total(), $perPage, $result->currentPage());
    return $paginator;
  }

  public function getById(int $id)
  {
    $query = $this->model->select();
    $result = $query->find($id);
    return $result;
  }

  public function create(array $data)
  {
    $data['created_at'] = Carbon::now();
    if (isset($data['user_auth_id'])) {
      $data['user_create_id'] = $data['user_auth_id'];
    }

    $serviceComment = $this->model->create($data);
    if ($serviceComment) {
      $serviceComment->load(['userCreate.person', 'userCreate.typeUser:id,nombre']);
      $serviceComment->created_at = Carbon::parse($serviceComment->created_at)->format('Y-m-d H:i:s');
    }

    return $serviceComment;
  }

  public function update(array $data, int $id)
  {
    $data['updated_at'] = Carbon::now();
    if (isset($data['user_auth_id'])) {
      $data['user_update_id'] = $data['user_auth_id'];
    }

    $serviceComment = $this->model->find($id);
    if ($serviceComment) {
      $serviceComment->fill($data);
      $serviceComment->save();
      $serviceComment->updated_at = Carbon::parse($serviceComment->updated_at)->format('Y-m-d H:i:s');
      return $serviceComment;
    }

    return null;
  }

  public function delete(int $id)
  {
    $serviceComment = $this->model->find($id);
    if ($serviceComment != null) {
      $serviceComment->is_active = 0;
      $serviceComment->save();
      $result = $serviceComment->delete();
      if ($result) {
        $serviceComment->deleted_st = Carbon::parse($serviceComment->deleted_at)->format('Y-m-d H:i:s');
        return $serviceComment;
      }
    }

    return false;
  }

  public function restore(int $id)
  {
    $serviceComment = $this->model->withTrashed()->find($id);
    if ($serviceComment != null && $serviceComment->trashed()) {
      $serviceComment->is_active = 1;
      $serviceComment->save();
      $result = $serviceComment->restore();
      if ($result) {
        return $serviceComment;
      }
    }

    return false;
  }
}
