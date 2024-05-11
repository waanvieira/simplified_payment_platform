<?php

declare(strict_types=1);

namespace App\UseCases\Transaction;

use App\Domain\Repositories\TransactionEntityRepositoryInterface;
use App\UseCases\DTO\Transaction\TransactionUpdateInputDto;

class TransactionUpdateUseCase
{

    public function __construct(
        protected TransactionEntityRepositoryInterface $repository
    ) {
    }

    public function execute($input)
    {
    }
}
