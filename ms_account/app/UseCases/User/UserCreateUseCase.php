<?php

declare(strict_types=1);

namespace App\UseCases\User;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Exceptions\BadRequestException;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\User\UserCreateInputDto;
use App\UseCases\DTO\User\UserCreateOutputDto;

class UserCreateUseCase
{
    public function __construct(
        protected UserEntityRepositoryInterface $repository,
        protected RabbitInterface $rabbitMqService
    ) {
        $this->repository = $repository;
        $this->rabbitMqService = $rabbitMqService;
    }

    public function execute(UserCreateInputDto $input): UserCreateOutputDto
    {
        $userEntity = User::create(
            name: $input->name,
            cpfCnpj: $input->cpfCnpj,
            email: $input->email,
            password: $input->password
        );

        if ($this->repository->findByEmail($input->email) || $this->repository->findByCpfCnpj($input->cpfCnpj)) {
            throw new BadRequestException('E-mail ou CPF/CNPJ  cadastrados, não é possível fazer o cadastro');
        }

        $user = $this->repository->insert($userEntity);
        $this->rabbitMqService->producer('userCreated', $user->toArray());

        return new UserCreateOutputDto(
            id: $user->id(),
            name: $user->name,
            cpf_cnpj: $user->cpfCnpj(),
            email: $user->email(),
            shopkeeper: $user->shopkeeper,
            // created_at: (string)$user->createdAt()
        );

    }
}
