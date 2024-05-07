<?php

declare(strict_types=1);

namespace App\UseCases\Message;

use App\Domain\Entities\Message;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\DTO\Message\MessageUpdateInputDto;
use App\UseCases\DTO\Message\MessageUpdateOutputDto;
use Illuminate\Database\Eloquent\Model;

class MessageUpdateUseCase
{
    public function __construct(
        protected MessageEntityRepositoryInterface $repository,
        protected NewsletterEntityRepositoryInterface $newsletterEntityRepository
    ) {
        $this->repository = $repository;
        $this->newsletterEntityRepository = $newsletterEntityRepository;
    }

    public function execute(MessageUpdateInputDto $input): MessageUpdateOutputDto
    {
        $message = Message::restore(
            id: $input->id,
            title: $input->title,
            message: $input->message,
            newsLetterId: $input->newsLetterId
        );

        $this->newsletterEntityRepository->findById($message->newsLetterId);
        $messageDb = $this->repository->update($message);

        return new MessageUpdateOutputDto(
            id: $messageDb->id,
            newsletter_id: $messageDb->newsLetterId,
            title: $messageDb->title,
            message: $messageDb->message,
            created_at: $messageDb->createdAt,
            // updated_at: $messageDb->updatedAt,
        );
    }
}
