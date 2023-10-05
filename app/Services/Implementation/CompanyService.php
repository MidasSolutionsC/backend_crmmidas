<?php

namespace App\Services\Implementation;

use App\Models\Company;
use App\Services\Interfaces\ICompany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService implements ICompany{

  private $model;

  public function __construct()
  {
    $this->model = new Company();
  }

  public function index($data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda
    
    $query = $this->model->query();

    $query = $this->model->with(['identificationDocument' => function ($subQuery) {
      $subQuery->select(
        'documentos_identificaciones.id', 
        'documentos_identificaciones.empresas_id', 
        'documentos_identificaciones.tipo_documentos_id', 
        'documentos_identificaciones.documento', 
        'documentos_identificaciones.reverso_documento',
        'TD.abreviacion as tipo_documentos_abreviacion'
      ); // Lista de columnas que deseas seleccionar
  
      $subQuery->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', 'TD.id');
    }]);

    $query->select(
      'empresas.*',
      'PS.nombre as paises_nombre',
    );

    $query->join('paises as PS', 'empresas.paises_id', 'PS.id');

    // Aplicar filtro de búsqueda si se proporciona un término
    $query->where(function ($query) use ($search) {
      if(!empty($search)){
        $query->orWhereHas('identificationDocument', function ($query) use ($search) {
            $query->select();
            $query->whereHas('typeDocument', function ($subquery) use ($search) {
                $subquery->select();
                if(!empty($search)){
                  $subquery->where('abreviacion', $search);
                }
            });

            if(!empty($search)){
              $query->where('documento', 'like', '%' . $search . '%');
            }
        });
      }

      if(!empty($search)){
        $query->where('razon_social', 'like', '%' . $search . '%')
        ->orWhere('nombre_comercial', 'like', '%' . $search . '%');
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
    // $query = $this->model->query();
    $query = $this->model->with(['identificationDocument' => function ($subQuery) {
      $subQuery->select(
        'documentos_identificaciones.id', 
        'documentos_identificaciones.empresas_id', 
        'documentos_identificaciones.tipo_documentos_id', 
        'documentos_identificaciones.documento', 
        'documentos_identificaciones.reverso_documento',
        'TD.abreviacion as tipo_documentos_abreviacion'
      ); // Lista de columnas que deseas seleccionar
  
      $subQuery->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', 'TD.id');
    }]);

    $result = $query->get();
    return $result;
  }

  public function search(array $data){
    $search = !empty($data['search'])? $data['search']: "";
    $typeDocumentId = !empty($data['tipo_documentos_id'])? $data['tipo_documentos_id']: null;
    $document = !empty($data['documento'])? $data['documento']: null;

    // $query = $this->model->query();
    $query = $this->model->with(['identificationDocument' => function ($subQuery) {
      $subQuery->select(
        'documentos_identificaciones.id', 
        'documentos_identificaciones.empresas_id', 
        'documentos_identificaciones.tipo_documentos_id', 
        'documentos_identificaciones.documento', 
        'documentos_identificaciones.reverso_documento',
        'TD.abreviacion as tipo_documentos_abreviacion'
      ); // Lista de columnas que deseas seleccionar
  
      $subQuery->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', 'TD.id');
    }]);

    $query->select(
      'empresas.*',
      // 'TD.nombre as tipo_documentos_nombre',
      // 'TD.abreviacion as tipo_documentos_abreviacion',
      'PS.nombre as paises_nombre',
    );

    $query->join('paises as PS', 'empresas.paises_id', 'PS.id');

     // BÚSQUEDA
     $query->where(function ($query) use ($search, $document, $typeDocumentId) {
      if(!is_null($typeDocumentId) || !is_null($document)){
        $query->orWhereHas('identificationDocument', function ($query) use ($document, $typeDocumentId) {
            $query->select();
            $query->whereHas('typeDocument', function ($subquery) use ($typeDocumentId) {
                $subquery->select();
                if(!is_null($typeDocumentId)){
                  $subquery->where('id', $typeDocumentId);
                }
            });
  
            if(!is_null($document)){
              $query->where('documento', 'like', '%' . $document . '%');
            }
        });
      }

      if(!empty($search)){
        $query->where('razon_social', 'like', '%' . $search . '%')
            ->orWhere('nombre_comercial', 'like', '%' . $search . '%');
      }
    });
  
    $query->take(25); // Limite de resultados
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->query();
    $query->with(['identificationDocument' => function ($subQuery) {
      $subQuery->select(
        'documentos_identificaciones.id', 
        'documentos_identificaciones.empresas_id', 
        'documentos_identificaciones.tipo_documentos_id', 
        'documentos_identificaciones.documento', 
        'documentos_identificaciones.reverso_documento',
        'TD.abreviacion as tipo_documentos_abreviacion'
      ); // Lista de columnas que deseas seleccionar
  
      $subQuery->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', 'TD.id');
    }]);

    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_create_id'] = $data['user_auth_id'];
    }
    $company = $this->model->create($data);
    if($company){
      $company->created_at = Carbon::parse($company->created_at)->format('Y-m-d H:i:s');
    }

    return $company;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    if(isset($data['user_auth_id'])){
      $data['user_update_id'] = $data['user_auth_id'];
    }
    $company = $this->model->find($id);
    if($company){
      $company->fill($data);
      $company->save();
      $company->updated_at = Carbon::parse($company->updated_at)->format('Y-m-d H:i:s');
      return $company;
    }

    return null;
  }

  public function delete(int $id){
    $company = $this->model->find($id);
    if($company != null){
      $company->is_active = 0;
      $company->save();
      $result = $company->delete();
      if($result){
        $company->deleted_st = Carbon::parse($company->deleted_at)->format('Y-m-d H:i:s');
        return $company;
      }
    }

    return false;
  }

  public function restore(int $id){
    $company = $this->model->withTrashed()->find($id);
    if($company != null && $company->trashed()){
      $company->is_active = 1;
      $company->save();
      $result = $company->restore();
      if($result){
        return $company;
      }
    }

    return false;
  }

}


?>