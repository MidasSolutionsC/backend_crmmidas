<?php

return [
  'required' => 'El campo :attribute es obligatorio.',
  'confirmed' => 'La confirmación de :attribute no coincide.',
  'boolean' => 'El campo :attribute debe ser verdadero o falso.',
  'unique' => 'El valor del campo :attribute ya está registrado en la base de datos.',
  'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
  'numeric' => 'El campo :attribute debe ser un número.',
  'integer' => 'El campo :attribute debe ser un número entero.',
  'date' => 'El campo :attribute debe ser una fecha válida.',
  'regex' => 'El formato del campo :attribute no es válido.',
  'in' => 'El valor seleccionado para :attribute no es válido.',
  'string' => 'El campo :attribute debe ser una cadena de texto.',
  'max' => [
    'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    'integer' => 'El campo :attribute no debe ser mayor que :max.',
    'numeric' => 'El campo :attribute no debe ser mayor que :max.',
  ],
  'min' => [
    'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    'integer' => 'El campo :attribute debe ser al menos :min.',
    'numeric' => 'El campo :attribute debe ser al menos :min.',
  ],
  'size' => [
    'string' => 'El campo :attribute debe tener exactamente :size caracteres.',
    'integer' => 'El campo :attribute debe ser :size.',
    'numeric' => 'El campo :attribute debe ser :size.',
  ],
  'json' => 'El campo :attribute debe ser una cadena JSON válida.',
  'array' => 'El campo :attribute debe ser un arreglo.',
  'file' => 'El campo :attribute debe ser un File.',
  'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.', // Mensaje para la regla 'file'

  // Agrega más mensajes personalizados aquí
];

?>