<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /*** @var User*/
    protected $entity;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->entity = $user;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createNewUser(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        return $this->entity->create($data);
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function getUser(string $email)
    {
        $user = $this->entity->where('email', $email)->firstOrFail();
        return $user;
    }
}
