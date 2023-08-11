<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleDocumentValidator{
  

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
      'nombre' => 'required',
      'archivo' => 'required',
    ];
  }

  private function rulesUpdate(){
    return [
      'ventas_id' => 'required',
      'nombre' => 'required',
      'archivo' => 'required',
    ];
  }

  private function messages(){
    return [
      'ventas_id.required' => 'El :attribute es requerido.',
      'nombre.required' => 'El :attribute es requerido.',
      'archivo.required' => 'La :attribute es requerido.',
    ];
  }
}


?>