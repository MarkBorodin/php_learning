<?php


namespace App\Repository;


interface UserRepositoryInterface
{
    public function getOne(int $userId): object;

    public function getAll(): array;
}