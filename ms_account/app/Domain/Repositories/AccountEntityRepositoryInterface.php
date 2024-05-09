<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Account;

interface AccountEntityRepositoryInterface extends BaseCrudEntityInterface
{
    public function updateBalance(Account $account) : bool;
    public function findByEmail(string $email);
    public function findByCpfCnpj(string $cpfCnpj);
}
