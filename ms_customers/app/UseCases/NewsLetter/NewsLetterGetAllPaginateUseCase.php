<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;

class NewsLetterGetAllPaginateUseCase
{
    public function __construct(
        protected NewsletterEntityRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(array $input = [])
    {
        return $this->repository->getAllPaginate();
    }
}
