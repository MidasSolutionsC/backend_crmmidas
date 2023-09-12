<?php

namespace App\Services\Interfaces;


interface IProduct{
  function index(array $data);
  function search(array $data);
  function getAll();
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>