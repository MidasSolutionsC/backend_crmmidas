<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IpAllowedValidator
{

  private $request;
  private $id;

  public function __construct(Request $request = null)
  {
    $this->request = $request;
    if ($request) {
      $this->id = $request->route('id');
    }
  }

  public function validate()
  {
    return Validator::make($this->request->all(), $this->rules());
  }

  private function rules()
  {
    return [
      'ip' => 'required|string|ip|unique:ip_permitidas,ip,' . $this->id . ',id,deleted_at,NULL',
      'sedes_id' => 'nullable|integer',
      'descripcion' => 'nullable|string|max:500',
      'fecha' => 'nullable|date:Y-m-d',
      'hora' => ['nullable', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
      'fecha_expiracion' => 'nullable|date:Y-m-d',
      'is_active' => 'nullable|boolean',
      'user_create_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
      'user_delete_id' => 'nullable|integer',
    ];
  }
}
