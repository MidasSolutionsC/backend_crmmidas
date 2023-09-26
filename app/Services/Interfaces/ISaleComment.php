<?php

namespace App\Services\Interfaces;

interface ISaleComment{
  function getAll();
  function getFilterBySale(int $saleId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>