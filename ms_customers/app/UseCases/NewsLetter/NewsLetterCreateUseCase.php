<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Entities\NewsLetter;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Exceptions\BadRequestException;
use App\UseCases\DTO\NewsLetter\NewsLetterCreateInputDto;
use App\UseCases\DTO\NewsLetter\NewsLetterCreateOutputDto;

class NewsLetterCreateUseCase
{
    protected $repository;
    protected $userEloquentRepository;

    public function __construct(
        NewsletterEntityRepositoryInterface $repository,
        UserEntityRepositoryInterface $userEloquentRepository
    ) {
        $this->repository = $repository;
        $this->userEloquentRepository = $userEloquentRepository;
    }

    public function execute(NewsLetterCreateInputDto $input) : NewsLetterCreateOutputDto
    {
        $user = $this->userEloquentRepository->findByEmail($input->email?? '');

        if (!$user || !$user->is_admin) {
            throw new BadRequestException('Usuário não tem permissão para criar lista');
        }

        $newsLetter = NewsLetter::create(
            name: $input->name,
            description: $input->description
        );

        $response = $this->repository->insert($newsLetter);

        return new NewsLetterCreateOutputDto(
            id: $response->id(),
            name: $response->name,
            description: $response->description,
            created_at: $response->createdAt
        );
    }
}
