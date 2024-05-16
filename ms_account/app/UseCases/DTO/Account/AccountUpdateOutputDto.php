<?php

namespace App\UseCases\DTO\Account;

class AccountUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $cpf_cnpj,
        public string $name,
        public string $email,
        public bool $shopkeeper,
        public float $balance,
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }
}
