<?php
namespace App\Http\Controllers;

use Illuminate\Filesystem\Filesystem;


class FileController extends Controller{

  public function showFile(string $fileName){
    $filesystem = new Filesystem();
    $publicPath = $filesystem->getAdapter()->getPathPrefix() . 'public/';
    if($publicPath){
      $file = $publicPath . $fileName;
      if(file_exists($file)){
        return response()->file($file);
      } else {
        return response('Archivo no encontrado', 404);
      }
    }
    
    return response('Directorio no encontrado', 404);
  }
}