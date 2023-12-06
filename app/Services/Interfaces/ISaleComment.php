<?php

namespace App\Services\Interfaces;

interface ISaleComment{
  function index(array $data);
  function getAll();
  function getFilterBySale(int $saleId);
  function getFilterBySaleDetail(int $saleDetailId);
  function getFilterBySaleDetailAsync(array $data);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>