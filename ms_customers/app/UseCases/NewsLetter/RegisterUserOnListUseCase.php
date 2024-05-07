<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter;

use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\Domain\Repositories\UserEntityRepositoryInterface;
use App\Exceptions\BadRequestException;

class RegisterUserOnListUseCase
{
    public function __construct(
        protected NewsletterEntityRepositoryInterface $repository,
        protected UserEntityRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function execute(array $input, string $id): void
    {
        $user = $this->userRepository->findByEmail($input['email']);
        if (!$user) {
            throw new BadRequestException("Não existe usuário com esse e-mail");
        }

        $this->repository->registerUserOnList($id, $user->id);
    }
}
