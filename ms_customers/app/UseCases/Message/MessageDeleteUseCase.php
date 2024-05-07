<?php

// declare(strict_types=1);

namespace App\UseCases\Message;

use App\Domain\Repositories\MessageEntityRepositoryInterface;

class MessageDeleteUseCase
{
    public function __construct(
        protected MessageEntityRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(string $id): bool
    {
        return $this->repository->delete($id);
    }
}
