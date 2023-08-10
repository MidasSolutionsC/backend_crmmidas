<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceValidator
{

  private $request;

  public function __construct(Request $request = null)
  {
    $this->request = $request;
  }

  public function validate(string $process = 'create')
  {
    if ($process == 'create') {
      return Validator::make($this->request->all(), $this->rulesCreate(), $this->messages());
    }
    if ($process == 'update') {
      return Validator::make($this->request->all(), $this->rulesUpdate(), $this->messages());
    }
  }

  private function rulesCreate()
  {
    return [
      'tipo_servicios_id' => 'required',
      'productos_id' => 'required',
      'instalaciones_id' => 'required',
      // 'datos_json' => 'required',
      'datos_json' => [
        'required',
        // function ($attribute, $value, $fail) {
        //   // Validar la estructura interna del JSON
        //   // if (!isset($value->ubicacion)) {
        //   //   $fail("El campo 'ubicacion' es requerido en el JSON.");
        //   // }

        //   // if (!isset($value->puerta)) {
        //   //   $fail("El campo 'puerta' es requerido en el JSON.");
        //   // }

        //   if (!isset($value->tipo)) {
        //     $fail("El campo 'tipo' es requerido en el JSON.");
        //   }

        //   if (isset($value->tipo)){
        //     $tvRules = [
        //       'tipo' => 'required|in:tv',
        //       'canal' => 'required|string',
        //       'duracion' => 'required|integer',
        //     ];

        //     $tvMessages = [
        //       'canal.required' => 'El canal es requerido.',
        //     ];

        //     $validator = Validator::make((array) $value, $tvRules, $tvMessages);
  
        //     if (isset($validator) && $validator->fails()) {
        //       $fail($validator);
        //     }
        //   }
        // },      
      ],
    ];
  }

  private function rulesUpdate()
  {
    return [
      'tipo_servicios_id' => 'required',
      'productos_id' => 'required',
      'instalaciones_id' => 'required',
      'datos_json' => 'required',
    ];
  }

  private function messages()
  {
    return [
      'tipo_servicios_id.required' => 'El :attribute es requerido.',
      'productos_id.required' => 'El :attribute es requerido.',
      'instalaciones_id.required' => 'El :attribute es requerido.',
      'datos_json.required' => 'El :attribute es requerido.',
      'datos_json.json' => 'El :attribute no es un json valido.',
      // 'datos_json' => [
      //   // 'required' => 'El :attribute es requerido.',
      //   'json' => 'El :attribute no es un json valido.',
      // ]
    ];
  }
}
