<?php

declare(strict_types=1);

namespace App\UseCases\User;

use App\Domain\Entities\User;
use App\Domain\Entities\UserEntity;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Domain\ValueObjects\CpfCnpj;
use App\UseCases\DTO\User\UserUpdateInputDto;
use App\UseCases\DTO\User\UserUpdateOutputDto;

class UserUpdateUseCase
{
    protected $repository;

    public function __construct(UserEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(UserUpdateInputDto $input) : UserUpdateOutputDto
    {
        $userEntity = User::restore(
            id: $input->id,
            name: $input->name,
            cpfCnpj: $input->cpfCnpj,
            email: $input->email
        );

        $this->repository->findById($userEntity->id);
        $userDb = $this->repository->update($userEntity);

        return new UserUpdateOutputDto(
            id: $userDb->id,
            cpf_cnpj: $userDb->cpfCnpj(),
            name: $userDb->name,
            email: $userDb->email,
            shopkeeper: $userDb->shopkeeper,
            created_at: $userDb->createdAt
        );
    }

}
