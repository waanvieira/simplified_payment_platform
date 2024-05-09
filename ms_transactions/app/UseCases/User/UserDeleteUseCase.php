<?php

declare(strict_types=1);

namespace App\UseCases\User;

use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\UseCases\DTO\User\UserCreateInputDto;

// use DateTimeImmutable;

class UserDeleteUseCase
{
    protected $repository;

    public function __construct(UserEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute($id) : bool
    {
        return $this->repository->delete($id);
    }
}
