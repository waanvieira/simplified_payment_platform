<?php

declare(strict_types=1);

namespace App\UseCases\Account;

use App\Domain\Entities\Account;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Exceptions\BadRequestException;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Account\AccountCreateInputDto;
use App\UseCases\DTO\Account\AccountCreateOutputDto;

class AccountCreateUseCase
{
    public function __construct(
        protected AccountEntityRepositoryInterface $repository,
        protected RabbitInterface $rabbitMqService
    ) {
    }

    public function execute(AccountCreateInputDto $input): AccountCreateOutputDto
    {
        $accountEntity = Account::create(
            name: $input->name,
            cpfCnpj: $input->cpfCnpj,
            email: $input->email,
            password: $input->password
        );

        if ($this->repository->findByEmail($input->email) || $this->repository->findByCpfCnpj($input->cpfCnpj)) {
            throw new BadRequestException('E-mail ou CPF/CNPJ  cadastrados, não é possível fazer o cadastro');
        }

        $account = $this->repository->insert($accountEntity);
        $this->rabbitMqService->producer('userCreated', $account->toArray());

        return new AccountCreateOutputDto(
            id: $account->id(),
            name: $account->name,
            cpf_cnpj: $account->cpfCnpj(),
            email: $account->email(),
            shopkeeper: $account->shopkeeper,
            balance: $account->balance,
            created_at: $account->createdAt()
        );
    }
}
