<?php 

namespace App\Services\Implementation;

use App\Models\Advertisement;
use App\Services\Interfaces\IAdvertisement;
use Illuminate\Support\Carbon;

class AdvertisementService implements IAdvertisement{
  
  private $model;

  public function __construct() {
    $this->model = new Advertisement();
  }

  public function getAll(){
    $query = $this->model->selectRaw('*, 
      CASE 
        WHEN tipo = "I" THEN "Interno" 
        WHEN tipo = "E" THEN "Externo" 
      ELSE "" END AS tipo_text'
    );
    $result = $query->get();
    return $result;
  }

  public function getById(int $id){
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

  public function create(array $data){
    $data['created_at'] = Carbon::now(); 
    $advertisement = $this->model->create($data);
    if($advertisement){
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

    return $advertisement;
  }

  public function update(array $data, int $id){
    $data['updated_at'] = Carbon::now(); 
    $advertisement = $this->model->find($id);
    if($advertisement){
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
      return $advertisement;
    }

    return null;
  }

  public function delete(int $id){
    $advertisement = $this->model->find($id);
    if($advertisement != null){
      $advertisement->save();
      $result = $advertisement->delete();
      if($result){
        $advertisement->deleted_st = Carbon::parse($advertisement->deleted_at)->format('Y-m-d H:i:s');
        return $advertisement;
      }
    }

    return false;
  }

  public function restore(int $id){
    $advertisement = $this->model->withTrashed()->find($id);
    if($advertisement != null && $advertisement->trashed()){
      $advertisement->save();
      $result = $advertisement->restore();
      if($result){
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
    }

    return false;
  }

}


?>