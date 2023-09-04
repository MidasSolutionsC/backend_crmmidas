<?php

namespace App\Services\Interfaces;

interface IUbigeo{
  function getAll();
  function search(array $data);
  function getById(string $ubigeo);
  function create(array $data);
  function update(array $data, string $ubigeo);
  function delete(string $ubigeo);
  function restore(string $ubigeo);
}


?>