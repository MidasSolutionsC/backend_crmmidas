<?php

namespace App\Services\Interfaces;


interface IBrand{
  function index(array $data);
  function getAll();
  function getFilterByCategory(int $categoryId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>