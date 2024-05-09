<?php

declare(strict_types=1);

namespace App\UseCases\Account;

use App\Domain\Repositories\AccountEntityRepositoryInterface;

class AccountGetAllUseCase
{
    protected $repository;

    public function __construct(AccountEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $input)
    {
        return $this->repository->getAllPaginate();
    }
}
