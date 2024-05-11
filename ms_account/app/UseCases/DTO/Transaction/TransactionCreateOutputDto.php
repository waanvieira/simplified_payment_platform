<?php

namespace App\UseCases\DTO\Transaction;

class TransactionCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $transaction_type,
        public string $payer_id,
        public string $payee_id,
        public float $value,
        public string $transaction_status,
        public string $created_at
    ) {
    }
}
