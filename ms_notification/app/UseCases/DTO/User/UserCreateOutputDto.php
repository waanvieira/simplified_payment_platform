<?php

namespace App\UseCases\DTO\User;

class UserCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $cpf_cnpj,
        public string $email,
        public bool $shopkeeper,
        public string $created_at = ''
    ) {
    }
}
