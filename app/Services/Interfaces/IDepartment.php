<?php

namespace App\Services\Interfaces;

interface IDepartment{
  function getAll();
  function getFilterByCountry(int $countryId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>