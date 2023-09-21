<?php

namespace App\Services\Interfaces;

interface IClient{
  function getAll();
  function getById(int $id);
  function getByPersonId(int $personId);
  function getByCompanyId(int $companyId);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>