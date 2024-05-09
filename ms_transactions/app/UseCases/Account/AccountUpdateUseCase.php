<?php

declare(strict_types=1);

namespace App\UseCases\Account;

use App\Domain\Entities\Account;
use App\Domain\Entities\AccountEntity;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Domain\ValueObjects\CpfCnpj;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Uuid;
use App\UseCases\DTO\Account\AccountUpdateInputDto;
use App\UseCases\DTO\Account\AccountUpdateOutputDto;

class AccountUpdateUseCase
{
    protected $repository;

    public function __construct(AccountEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(AccountUpdateInputDto $input) : AccountUpdateOutputDto
    {
        $AccountEntity = Account::restore(
            id: new Uuid($input->id),
            name: $input->name,
            cpfCnpj: new CpfCnpj($input->cpfCnpj),
            email: new Email($input->email),
        );

        $this->repository->findById($input->id);
        $account = $this->repository->update($AccountEntity);

        return new AccountUpdateOutputDto(
            id: $account->id(),
            cpf_cnpj: $account->cpfCnpj(),
            name: $account->name,
            email: $account->email(),
            shopkeeper: $account->shopkeeper,
            balance: $account->balance,
            created_at: $account->createdAt()
        );
    }

}
