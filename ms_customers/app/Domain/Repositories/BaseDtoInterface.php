<?php

namespace App\Domain\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseDtoInterface
{
    public function model(): Model;
    public function getAll(string $filter = '', $order = 'DESC'): array;
    public function getAllPaginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15);
}
