<?php

namespace App\Services\Interfaces;

interface IAddress{
  function getAll();
  function getFilterByCompany(int $companyId);
  function getFilterByPerson(int $personId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>