<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\UserEntity;
use Illuminate\Database\Eloquent\Model;

interface BaseCrudInterface
{
    public function model(): Model;
    public function insert(array $input): Model;
    public function findById(string $id): Model;
    public function update(array $input, string $id): Model;
    public function delete(string $id): bool;
    public function getAll(string $filter = '', $order = 'DESC'): array;
    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15);
}
