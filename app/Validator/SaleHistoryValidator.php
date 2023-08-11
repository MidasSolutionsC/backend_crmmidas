<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleHistoryValidator{
  

  /**
   * @var Request
   */
  private $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function validate(string $process = 'create'){
    if($process == 'create'){
      return Validator::make($this->request->all(), $this->rulesCreate(), $this->messages());
    }
    if($process == 'update'){
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
  }

  private function rulesCreate(){
    return [
      'ventas_id' => 'required',
      'user_create_id' => 'required',
      // 'tipo' => 'required',
    ];
  }

  private function rulesUpdate(){
    return [
      'ventas_id' => 'required',
      'user_create_id' => 'required',
      // 'tipo' => 'required',
    ];
  }

  private function messages(){
    return [
      'ventas_id.required' => 'El :attribute es requerido.',
      'user_create_id.required' => 'El :attribute es requerido.',
      'tipo.required' => 'El :attribute es requerido.',
    ];
  }
}


?>