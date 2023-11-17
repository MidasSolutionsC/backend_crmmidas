<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IUser {
  function index($data);
  function getAll();
  function getAllServerSide(array $data);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}

?>