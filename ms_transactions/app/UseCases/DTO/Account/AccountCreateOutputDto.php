<?php

namespace App\UseCases\DTO\Account;

class AccountCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $cpf_cnpj,
        public string $email,
        public bool $shopkeeper,
        public float $balance,
        public string $created_at = ''
    ) {
    }
}
