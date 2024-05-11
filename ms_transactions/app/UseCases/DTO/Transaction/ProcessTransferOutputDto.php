<?php

namespace App\UseCases\DTO\Transaction;

class ProcessTransferOutputDto
{
    public function __construct(
        public string $id,
        public string $transactionType,
        public string $payerId,
        public string $payeeId,
        public float $value,
        public string $transactionStatus
    ) {
    }
}
