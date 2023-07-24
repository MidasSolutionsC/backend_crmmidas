<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoDocumento;
use Illuminate\Support\Carbon;

class TipoDocumentoController extends Controller{

  public function index(){
    $data = TipoDocumento::all();
    return response()->json($data);
  }

  public function show($id){
    $tipoDocumento = TipoDocumento::find($id);
    return response()->json($tipoDocumento);
  }

  public function store(Request $request){
    $this->validate($request, [
      'nombre' => 'required',
      'abreviacion' => 'required'
    ]);
     
    $tipoDocumento = new TipoDocumento();
    $tipoDocumento->nombre = $request->input('nombre');
    $tipoDocumento->abreviacion = $request->input('abreviacion');
    $tipoDocumento->fecharegistro = Carbon::now();
    $tipoDocumento->save();

    return response()->json($tipoDocumento);
  }

  public function update(Request $request, $id){
    $this->validate($request, [
      'nombre' => 'required|unique:tipodocumento,nombre',
      'abreviacion' => 'required'
    ]);
     
    $tipoDocumento = TipoDocumento::find($id);
    $tipoDocumento->nombre = $request->input('nombre');
    $tipoDocumento->abreviacion = $request->input('abreviacion');

    if($request->filled('estado')){
      $tipoDocumento->estado = $request->input('estado');
    }

    $tipoDocumento->save();
    return response()->json($tipoDocumento);
  }

  public function destroy($id){
    $tipoDocumento = TipoDocumento::find($id);
    $tipoDocumento->delete();
    return response()->json('tipoDocumento Deleted Successfully');
  }
}