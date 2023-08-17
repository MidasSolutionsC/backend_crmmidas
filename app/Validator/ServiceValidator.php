<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceValidator{

  private $request;
  private $id;

  public function __construct(Request $request = null) {
    $this->request = $request;
    if ($request) {
      $this->id = $request->route('id');
    }
  }

  public function validate(){
    return Validator::make($this->request->all(), $this->rules());
  }

  private function rules() {
    return [
      'tipo_servicios_id' => 'required|integer',
      'productos_id' => 'required|integer',
      'instalaciones_id' => 'required|integer',
      'promociones_id' => 'nullable|integer',
      'observacion' => 'nullable|string',
      'fecha_cierre' => 'nullable|date:Y-m-d',
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
      'tipo_estados_id' => 'nullable|integer',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'required|integer',
      'user_update_id' => 'integer',
      'user_delete_id' => 'integer',
    ];
  }
}
