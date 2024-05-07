<?php

// declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;

class NewsLetterDeleteUseCase
{
    public function __construct(
        protected NewsletterEntityRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(string $id): bool
    {
        return $this->repository->delete($id);
    }
}
