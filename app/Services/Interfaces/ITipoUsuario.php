<?php

namespace App\Services\Interfaces;


interface ITipoUsuario{
  function getAll();
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);

  /**
   * @param int $id
   * @return boolean
   * 
   */
  function restore(int $id);
}


?>