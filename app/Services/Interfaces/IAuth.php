<?php

namespace App\Services\Interfaces;

interface IAuth {
  function login(array $data);
  function logout(int $id);
}

?>