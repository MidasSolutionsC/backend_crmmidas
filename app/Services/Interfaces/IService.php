<?php

namespace App\Services\Interfaces;


interface IService{
  function index(array $data);
  function search(array $data);
  function getAll();
  function getByTypeService(int $typeServiceId);
  function getByPromotion(int $promotionId);
  function getById(int $id);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}


?>