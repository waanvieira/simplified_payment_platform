<?php

declare(strict_types=1);

namespace App\UseCases\User;

use App\Domain\Repositories\UserEntityRepositoryInterface;

class UserGetAllUseCase
{
    protected $repository;

    public function __construct(UserEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $input)
    {
        return $this->repository->getAllPaginate();
    }
}
