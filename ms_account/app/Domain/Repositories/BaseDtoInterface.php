<?php

namespace App\Domain\Repositories;

interface BaseDtoInterface
{
    public function getAll(string $filter = '', $order = 'DESC'): array;

    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15);
}
