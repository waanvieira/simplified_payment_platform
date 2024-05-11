<?php

declare(strict_types=1);

namespace App\UseCases\Account;

use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\UseCases\DTO\Account\AccountCreateOutputDto;

class AccountFindByIdUseCase
{
    protected $repository;

    public function __construct(AccountEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $id): AccountCreateOutputDto
    {
        $account = $this->repository->findById($id);
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
