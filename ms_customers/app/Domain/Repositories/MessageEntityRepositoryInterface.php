<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Message;

interface MessageEntityRepositoryInterface
{
    public function insert(Message $Message): Message;

    public function findById(string $id): Message;

    public function getAllPaginate(string $filter = '', $order = 'DESC');

    public function getAll(string $filter = '', $order = 'DESC'): array;

    public function update(Message $Message): Message;

    public function delete(string $id): bool;
}
