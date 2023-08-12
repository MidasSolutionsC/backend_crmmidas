<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactValidator{
  

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
      'tipo' => 'required|string|size:3',
      'contacto' => 'required|string|max:60',
    ];
  }

  private function rulesUpdate(){
    return [
      'tipo' => 'required|string|size:3',
      'contacto' => 'required|string:60',
    ];
  }

  private function messages(){
    return [
      'tipo' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'size' => 'El :attribute debe tener :size caracteres.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
      'contacto' => [
        'required' => 'El :attribute es requerido.',
        'string' => 'El :attribute no es un texto valido.',
        'size' => 'El :attribute debe tener :size caracteres.',
        'max' => 'El :attribute debe tener como máximo :max caracteres.',
      ],
    ];
  }
}


?>