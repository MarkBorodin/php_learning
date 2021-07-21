<?php


namespace App\Service\User;


use App\Repository\UserRepositoryInterface;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}