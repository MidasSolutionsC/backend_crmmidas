<?php

namespace App\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportValidator
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

  public function setRequest(array  $data, int $id = null)
  {
    $this->request = new Request();
    $this->request->replace($data);
    $this->id = $id;
  }

  public function validate()
  {
    return Validator::make($this->request->all(), $this->rules());
  }


  private function rules()
  {
    return [
      'fecha_inicio' => 'required|date:Y-m-d',
      'fecha_fin' => 'required|date:Y-m-d',

    ];
  }
}
