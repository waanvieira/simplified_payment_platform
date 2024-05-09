<?php

namespace App\UseCases\DTO\Account;

class AccountCreateInputDto
{
    public function __construct(
        public string $name,
        public string $cpfCnpj,
        public string $email,
        public string $password
    ) {
    }
}
