<?php

namespace App\Services\Interfaces;

interface IPerson{
  function index(array $data);
  function getAll();
  function search(array $data);
  function getById(int $id);
  function getByIdentification(array $data);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>