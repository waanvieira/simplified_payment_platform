<?php

namespace App\UseCases\DTO\User;

class UserCreateInputDto
{
    public function __construct(
        public string $name,
        public string $cpfCnpj,
        public string $email,
        public string $password
    ) {
    }
}
