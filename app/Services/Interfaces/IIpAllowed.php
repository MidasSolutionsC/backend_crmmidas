<?php

namespace App\Services\Interfaces;

interface IIpAllowed
{
  function getAll();
  function getById(int $id);
  function getFilterByIP(string $ip);
  function create(array $data);
  function update(array $data, int $id);
  function delete(int $id);
  function restore(int $id);
}
