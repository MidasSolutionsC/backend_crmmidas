<?php

namespace App\Services\Interfaces;

interface IProductPrice{
  function getAll();
  function getFilterByProduct(int $productId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>