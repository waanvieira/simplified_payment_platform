<?php

namespace App\UseCases\DTO\User;

class UserUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $cpf_cnpj,
        public string $name,
        public string $email,
        public bool $shopkeeper = false,
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }
}
