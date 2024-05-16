<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Transaction;

interface TransactionEntityRepositoryInterface extends BaseCrudEntityInterface
{
    public function updateBalance(Transaction $transaction): bool;

    public function transactionAproved(Transaction $transaction): bool;

    public function transactionReproved(Transaction $transaction): bool;
}
