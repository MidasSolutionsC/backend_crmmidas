<?php

namespace App\Services\Implementation;

use App\Models\Person;
use App\Services\Interfaces\IPerson;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


class PersonService implements IPerson{

  private $model;

  public function __construct()
  {
    $this->model = new Person();
  }

  public function index($data){
    $page = !empty($data['page'])? $data['page'] : 1; // Número de página
    $perPage = !empty($data['perPage']) ? $data['perPage'] : 10; // Elementos por página
    $search = !empty($data['search']) ? $data['search']: ""; // Término de búsqueda
    
    // $query = $this->model->query();
    $query = $this->model->with(['identifications']);

    $query->select(
      'personas.*',
      'PS.nombre as paises_nombre',

    );

    $query->join('paises as PS', 'personas.paises_id', 'PS.id');

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
        $query->where('nombres', 'like', '%' . $search . '%')
            ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
            ->orWhere('apellido_materno', 'like', '%' . $search . '%')
            ->orWhere('PS.nombre', 'like', '%' . $search . '%')
            ->orWhere('nacionalidad', 'like', '%' . $search . '%');
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
    // $query = $this->model->with('identificationDocument')->select();
    $query = $this->model->with(['client.bankAccounts', 'contacts', 'identifications', 'addresses'])->select();

    $result = $query->get();
    return $result;
  }

  public function search(array $data){
    $search = !empty($data['search'])? $data['search']: "";
    $typeDocumentId = !empty($data['tipo_documentos_id'])? $data['tipo_documentos_id']: null;
    $document = !empty($data['documento'])? $data['documento']: null;

    $query = $this->model->query();

    $query = $this->model->with(['client.bankAccounts', 'contacts', 'identifications', 'addresses'])->select();


    $query->select(
      'personas.*',
      'PS.nombre as paises_nombre',

    );

    $query->join('paises as PS', 'personas.paises_id', 'PS.id');

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
        $query->where('nombres', 'like', '%' . $search . '%')
            ->orWhere('apellido_paterno', 'like', '%' . $search . '%')
            ->orWhere('apellido_materno', 'like', '%' . $search . '%')
            ->orWhere('PS.nombre', 'like', '%' . $search . '%')
            ->orWhere('nacionalidad', 'like', '%' . $search . '%');
      }
    });


    $query->take(25); // Limite de resultados
    $result = $query->get();
    
    return $result;
  }

  public function getByIdentification(array $data){
    $typeDocumentId = !empty($data['tipo_documentos_id'])? $data['tipo_documentos_id']: null;
    $document = !empty($data['documento'])? $data['documento']: null;

    $query = $this->model->query();

    $query = $this->model->with(['client.bankAccounts', 'contacts', 'identifications', 'addresses'])->select();


    $query->select(
      'personas.*',
      'PS.nombre as paises_nombre',
    );

    $query->join('paises as PS', 'personas.paises_id', 'PS.id');

    // BÚSQUEDA
    $query->where(function ($query) use ($document, $typeDocumentId) {
      if(!is_null($typeDocumentId) || !is_null($document)){
        $query->orWhereHas('identificationDocument', function ($query) use ($document, $typeDocumentId) {
            $query->whereHas('typeDocument', function ($subquery) use ($typeDocumentId) {
                if(!is_null($typeDocumentId)){
                  $subquery->where('id', $typeDocumentId);
                }
            });
  
            if(!is_null($document)){
              $query->where('documento', $document);
            }
        });
      }
    });

    $result = $query->first();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->query();
    $query->with(['client.bankAccounts', 'contacts', 'identifications', 'addresses']);

    $result = $query->find($id);
    return $result;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    $person = $this->model->create($data);
    if($person){
      $person->created_at = Carbon::parse($person->created_at)->format('Y-m-d H:i:s');
      $person->load('identifications', 'contacts', 'addresses');
      $person->paises_nombre = $person->country->nombre;
      unset($person->country);

    }

    return $person;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $person = $this->model->find($id);
    if($person){
      $person->fill($data);
      $person->save();
      $person->updated_at = Carbon::parse($person->updated_at)->format('Y-m-d H:i:s');
      $person->load('identifications', 'contacts', 'addresses');
      $person->paises_nombre = $person->country->nombre;
      unset($person->country);
      return $person;
    }

    return null;
  }

  public function delete(int $id){
    $person = $this->model->find($id);
    if($person != null){
      $person->save();
      $result = $person->delete();
      if($result){
        $person->deleted_st = Carbon::parse($person->deleted_at)->format('Y-m-d H:i:s');
        return $person;
      }
    }

    return false;
  }

  public function restore(int $id){
    $person = $this->model->withTrashed()->find($id);
    if($person != null && $person->trashed()){
      $person->save();
      $result = $person->restore();
      if($result){
        return $person;
      }
    }

    return false;
  }

}


?>