<?php

namespace App\UseCases\DTO\Transaction;

class TransferAprovedInputDto
{
    public function __construct(
        public string $transactionId,
        public string $payerId,
        public string $payeeId,
        public float $value,
    ) {
    }
}
