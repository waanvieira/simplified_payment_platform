<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;
use Illuminate\Database\Eloquent\Model;

interface UserEntityRepositoryInterface
{
    public function insert(User $user): User;

    public function findById(string $userId): User;

    public function getAllPaginate(string $filter = '', $order = 'DESC');

    public function getAll(string $filter = '', $order = 'DESC'): array;

    public function update(User $user): User;

    public function delete(string $UserEntityId): bool;

    public function findByEmail(string $email);

    public function findByCpfCnpj(string $cpfCnpj);
}
