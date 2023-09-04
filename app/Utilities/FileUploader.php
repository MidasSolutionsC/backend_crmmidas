<?php

namespace App\Utilities;

use Exception;
use Illuminate\Http\UploadedFile;

class FileUploader
{
  public static function upload(UploadedFile $file, $destinationPath, $allowedExtensions = []){
    // Validar extensiones permitidas si se proporciona una lista
    if (!empty($allowedExtensions)) {
      $extension = $file->getClientOriginalExtension();
      if (!in_array($extension, $allowedExtensions)) {
        throw new \Exception('La extensión del archivo no está permitida.');
      }
    }

    // Generar un nombre único para el archivo
    $fileName = $file->hashName();
    
    // Mover el archivo al destino especificado
    $file->move($destinationPath, $fileName);
    return $fileName;
  }

  public static function uploadMultiple(array $files, $destinationPath, $allowedExtensions = []){
      $uploadedFiles = [];

      foreach ($files as $file) {
          if ($file instanceof UploadedFile) {
              $uploadedFiles[] = self::upload($file, $destinationPath, $allowedExtensions);
          }
      }

      return $uploadedFiles;
  }
}


?>