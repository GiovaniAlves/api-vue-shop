<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function createNewUser(array $data);
    public function getUser(string $email);
}
