<?php

namespace App\Services\Interfaces;

interface IDistrict{
  function getAll();
  function getFilterByProvince(int $provinceId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>