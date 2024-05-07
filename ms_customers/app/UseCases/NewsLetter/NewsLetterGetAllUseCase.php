<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;

class NewsLetterGetAllUseCase
{
    public function __construct(
        protected NewsletterEntityRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(array $input = []) : array
    {
        return $this->repository->getAll();
    }
}
