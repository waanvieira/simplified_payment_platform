<?php

namespace App\UseCases\DTO\Account;

class AccountUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $cpfCnpj = '',
        public string $name = '',
        public string $email = '',
    ) {

    }
}
