<?php

namespace App\Services\Interfaces;

interface IInstallation{
  function getAll();
  function search(array $data);
  function getBySale(int $saleId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>