<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\NewsLetter;
interface NewsletterEntityRepositoryInterface
{
    public function insert(NewsLetter $NewsLetter): NewsLetter;

    public function findById(string $NewsLetterId): NewsLetter;

    public function getAllPaginate(string $filter = '', $order = 'DESC');

    public function getAll(string $filter = '', $order = 'DESC'): array;

    public function update(NewsLetter $NewsLetter): NewsLetter;

    public function delete(string $NewsLetterId): bool;

    public function RegisterUserOnList(string $newsLetterId, string $idUser): void;
}
