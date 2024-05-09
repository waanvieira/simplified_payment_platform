<?php

declare(strict_types=1);

namespace App\UseCases\User;

use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\UseCases\DTO\User\UserCreateInputDto;
use App\UseCases\DTO\User\UserCreateOutputDto;

class UserFindByIdUseCase
{
    protected $repository;

    public function __construct(UserEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $id): UserCreateOutputDto
    {
        $user = $this->repository->findById($id);
        return new UserCreateOutputDto(
            id: $user->id,
            name: $user->name,
            cpf_cnpj: $user->cpfCnpj,
            email: $user->email,
            shopkeeper: $user->shopkeeper,
            created_at: $user->createdAt
        );
    }
}
