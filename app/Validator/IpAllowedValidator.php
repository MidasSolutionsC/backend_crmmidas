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
      'descripcion' => 'required|string|max:255,' . $this->id . ',id,deleted_at,NULL',
    ];
  }
}
