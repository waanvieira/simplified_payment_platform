<?php

declare(strict_types=1);

namespace App\UseCases\Message;

use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\UseCases\DTO\Message\MessageUpdateOutputDto;

class MessageFindByIDUseCase
{
    protected $repository;

    public function __construct(MessageEntityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $id): MessageUpdateOutputDto
    {
        $messageDb = $this->repository->findById($id);
        return new MessageUpdateOutputDto(
            id: $messageDb->id,
            newsletter_id: $messageDb->newsLetterId,
            title: $messageDb->title,
            message: $messageDb->message,
            created_at: $messageDb->createdAt,
        );
    }
}
