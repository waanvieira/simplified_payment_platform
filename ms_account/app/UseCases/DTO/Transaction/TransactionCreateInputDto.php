<?php

namespace App\UseCases\DTO\Transaction;

class TransactionCreateInputDto
{
    public function __construct(
        public string $payerId,
        public string $payeeId,
        public float $value,
    ) {
    }
}
