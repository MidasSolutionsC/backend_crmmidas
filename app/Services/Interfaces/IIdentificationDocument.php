<?php

namespace App\Services\Interfaces;

interface IIdentificationDocument{
  function getAll();
  function getFilterByPerson(int $personId);
  function getFilterByCompany(int $companyId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>