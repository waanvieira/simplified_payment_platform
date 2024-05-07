<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\DTO\NewsLetter\NewsLetterUpdateOutputDto;

class NewsLetterFindByIdUseCase
{
    public function __construct(
        protected NewsletterEntityRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function execute(string $id) : NewsLetterUpdateOutputDto
    {
        $newsLetter = $this->repository->findById($id);

        return new NewsLetterUpdateOutputDto(
            id: $newsLetter->id(),
            name: $newsLetter->name,
            description: $newsLetter->description,
            created_at: $newsLetter->createdAt,
        );
    }
}
