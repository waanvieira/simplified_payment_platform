<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Entity;

interface BaseCrudEntityInterface
{
    public function insert(Entity $entity): Entity;
    public function findById(string $id): Entity;
    public function update(Entity $entity): Entity;
    public function delete(string $id): bool;
    public function getAll(string $filter = '', $order = 'DESC'): array;
    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15);
}
