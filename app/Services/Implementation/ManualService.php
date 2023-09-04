<?php 

namespace App\Services\Implementation;

use App\Models\Manual;
use App\Services\Interfaces\IManual;
use Illuminate\Support\Carbon;

class ManualService implements IManual{
  
  private $model;

  public function __construct() {
    $this->model = new Manual();
  }

  public function getAll(){
    $query = $this->model->selectRaw('*, 
      CASE 
        WHEN tipo = "S" THEN "Manual de Software" 
        WHEN tipo = "B" THEN "Gestión de Backlog" 
        WHEN tipo = "M" THEN "Vodafone Micropyme"
        WHEN tipo = "R" THEN "Vodafone Residencial"
        WHEN tipo = "O" THEN "Otro"
      ELSE "" END AS tipo_text'
    );

    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
    $query = $this->model->select();
    $manual = $query->find($id);
    switch ($manual->tipo) {
      case 'S':
        $manual->tipo_text = 'Manual de Software';
        break;
      case 'B':
        $manual->tipo_text = 'Gestión de Backlog';
        break;
      case 'M':
        $manual->tipo_text = 'Vodafone Micropyme';
        break;
      case 'R':
        $manual->tipo_text = 'Vodafone Residencial';
        break;
      case 'O':
        $manual->tipo_text = 'Otro';
        break;
      default:
        $manual->tipo_text = '';
        break;
    }
    return $manual;
  }

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    $manual = $this->model->create($data);
    if($manual){
      $manual->created_at = Carbon::parse($manual->created_at)->format('Y-m-d H:i:s');
      switch ($manual->tipo) {
        case 'S':
          $manual->tipo_text = 'Manual de Software';
          break;
        case 'B':
          $manual->tipo_text = 'Gestión de Backlog';
          break;
        case 'M':
          $manual->tipo_text = 'Vodafone Micropyme';
          break;
        case 'R':
          $manual->tipo_text = 'Vodafone Residencial';
          break;
        case 'O':
          $manual->tipo_text = 'Otro';
          break;
        default:
          $manual->tipo_text = '';
          break;
      }
    }

    return $manual;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $manual = $this->model->find($id);
    if($manual){
      $manual->fill($data);
      $manual->save();
      $manual->updated_at = Carbon::parse($manual->updated_at)->format('Y-m-d H:i:s');
      switch ($manual->tipo) {
        case 'S':
          $manual->tipo_text = 'Manual de Software';
          break;
        case 'B':
          $manual->tipo_text = 'Gestión de Backlog';
          break;
        case 'M':
          $manual->tipo_text = 'Vodafone Micropyme';
          break;
        case 'R':
          $manual->tipo_text = 'Vodafone Residencial';
          break;
        case 'O':
          $manual->tipo_text = 'Otro';
          break;
        default:
          $manual->tipo_text = '';
          break;
      }
      return $manual;
    }

    return null;
  }

  public function delete(int $id){
    $manual = $this->model->find($id);
    if($manual != null){
      $manual->save();
      $result = $manual->delete();
      if($result){
        $manual->deleted_st = Carbon::parse($manual->deleted_at)->format('Y-m-d H:i:s');
        return $manual;
      }
    }

    return false;
  }

  public function restore(int $id){
    $manual = $this->model->withTrashed()->find($id);
    if($manual != null && $manual->trashed()){
      $manual->save();
      $result = $manual->restore();
      if($result){
        switch ($manual->tipo) {
          case 'S':
            $manual->tipo_text = 'Manual de Software';
            break;
          case 'B':
            $manual->tipo_text = 'Gestión de Backlog';
            break;
          case 'M':
            $manual->tipo_text = 'Vodafone Micropyme';
            break;
          case 'R':
            $manual->tipo_text = 'Vodafone Residencial';
            break;
          case 'O':
            $manual->tipo_text = 'Otro';
            break;
          default:
            $manual->tipo_text = '';
            break;
        }
        return $manual;
      }
    }

    return false;
  }

}


?>