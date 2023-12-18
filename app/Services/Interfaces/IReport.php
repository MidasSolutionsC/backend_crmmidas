<?php

namespace App\Services\Interfaces;

interface IReport
{
  function salesByBrand(array $data);
  function salesByCoordinator(array $data);
  function salesBySeller(array $data);
}
