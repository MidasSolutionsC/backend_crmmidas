<?php

namespace App\Services\Interfaces;

interface ITypeStatus{
  function index(array $data);
  function getAll();
  function getById(int $id);
  function getByName(string $name);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>