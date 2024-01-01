<?php

namespace App\Services\Implementation;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Group;
use App\Models\Person;
use App\Models\TypeUser;
use App\Models\User;
use App\Services\Interfaces\IReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportService implements IReport
{

  private $model;
  private $modelProduct;
  private $modelBrand;



  public function __construct()
  {
    $this->model = new Sale();
    $this->modelProduct = new Product();
    $this->modelBrand = new Brand();
  }

  public function salesByBrand(array $data)
  {

    $fecha_inicio = $data['fecha_inicio'];
    $fecha_fin = $data['fecha_fin'];

    $ventasPorMarca = Brand::select('marcas.nombre')
      ->addSelect(DB::raw('COUNT(ventas_detalles.id) as total_ventas'))
      ->join('productos', 'marcas.id', '=', 'productos.marcas_id')
      ->join('ventas_detalles', 'ventas_detalles.productos_id', '=', 'productos.id')
      ->join('ventas', 'ventas.id', '=', 'ventas_detalles.ventas_id')
      ->where('ventas.deleted_at', null)
      ->where('productos.deleted_at', null)
      ->where('marcas.deleted_at', null)
      ->whereBetween('ventas.fecha', [$fecha_inicio, $fecha_fin])
      ->groupBy('marcas.id', 'marcas.nombre')
      ->get();

    return $ventasPorMarca;
  }

  public function salesBySeller(array $data)
  {

    $fecha_inicio = $data['fecha_inicio'];
    $fecha_fin = $data['fecha_fin'];

    $ventasPorComercial = User::select(DB::raw('CONCAT(personas.nombres," ",personas.apellido_materno," ",personas.apellido_paterno) AS nombre'))
      ->addSelect(DB::raw('COUNT(ventas.id) as total_ventas'))
      ->join('personas', 'usuarios.personas_id', '=', 'personas.id')
      ->join('ventas', 'ventas.user_create_id', '=', 'usuarios.id')
      ->where('ventas.deleted_at', null)
      ->where('usuarios.deleted_at', null)
      ->whereBetween('ventas.fecha', [$fecha_inicio, $fecha_fin])
      ->groupBy('usuarios.id', 'personas.nombres', 'personas.apellido_materno', 'personas.apellido_paterno')
      ->get();

    return $ventasPorComercial;
  }

  public function salesByCoordinator(array $data)

  {

    $fecha_inicio = $data['fecha_inicio'];
    $fecha_fin = $data['fecha_fin'];

    $count_user = User::where('deleted_at', null)->count();
    $count_product = Product::where('deleted_at', null)->count();
    $count_sale = Sale::where('deleted_at', null)->count();



    $reporteVentas = Group::all()->map(function ($grupo) use ($fecha_inicio, $fecha_fin) {
      // $coordinador = User::find($grupo->coordinador_id);

      $ventasVendedores = User::select(
        DB::raw('CONCAT(personas.nombres," ",personas.apellido_materno," ",personas.apellido_paterno) AS nombre'),
        'usuarios.id as id'
      )
        ->join('integrantes', 'usuarios.id', '=', 'integrantes.usuarios_id')
        ->join('personas', 'usuarios.personas_id', '=', 'personas.id')
        ->join('grupos', 'integrantes.grupos_id', '=', 'grupos.id')
        ->where('integrantes.deleted_at', null)
        ->where('grupos.id', $grupo->id)
        ->where('grupos.deleted_at', null)
        ->get()->map(function ($vendedor) use ($fecha_inicio, $fecha_fin) {
          return [
            'vendedor' => htmlspecialchars($vendedor->nombre),
            'total_ventas' => Sale::where('user_create_id', $vendedor->id)->where('ventas.deleted_at', null)->whereBetween('fecha', [$fecha_inicio, $fecha_fin])->count(),
          ];
        });

      return [
        'coordinacion' => htmlspecialchars($grupo->nombre),
        'vendedores' => $ventasVendedores


      ];
    });

    return [
      'data' => $reporteVentas,
      'count_user' => $count_user,
      'count_product' => $count_product,
      'count_sale' => $count_sale,
    ];
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
