<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    /*** @var UserRepositoryInterface */
    protected $userRepository;

    /*** @param UserRepositoryInterface $userRepository */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createNewUser(array $data)
    {
        return $this->userRepository->createNewUser($data);
    }

    /**
     * @param string $email
     * @return mixed
     */
    public function getUser(string $email)
    {
        return $this->userRepository->getUser($email);
    }
}
