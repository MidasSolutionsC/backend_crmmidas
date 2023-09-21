<?php

namespace App\Services\Interfaces;

interface ICompany{
  function getAll();
  function search(array $data);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>