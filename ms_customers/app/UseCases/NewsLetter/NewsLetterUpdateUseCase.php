<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Exceptions\BadRequestException;
use App\UseCases\DTO\NewsLetter\NewsLetterUpdateInputDto;
use App\UseCases\DTO\NewsLetter\NewsLetterUpdateOutputDto;

class NewsLetterUpdateUseCase
{
    public function __construct(
        protected NewsletterEntityRepositoryInterface $repository,
        protected UserEntityRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function execute(NewsLetterUpdateInputDto $input): NewsLetterUpdateOutputDto
    {
        $newsLetter = NewsLetter::restore($input->id, $input->name, $input->description, $input->email);
        $user = $this->userRepository->findByEmail($input->email ?? '');

        if (!$user || !$user->is_admin) {
            throw new BadRequestException('Usuário não tem permissão para atualizar lista');
        }

        $response = $this->repository->findById($input->id);
        $newsLetter->update($input->name, $input->description);
        $response = $this->repository->update($newsLetter);

        return new NewsLetterUpdateOutputDto(
            id: $response->id(),
            name: $response->name,
            description: $response->description,
            created_at: $response->createdAt,
            updated_at: $response->createdAt ?? ''
        );
    }
}
