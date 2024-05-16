<?php

namespace App\Domain\Repositories;

interface AccountEntityRepositoryInterface extends BaseCrudEntityInterface
{
    public function findByEmail(string $email);

    public function findByCpfCnpj(string $cpfCnpj);
}
