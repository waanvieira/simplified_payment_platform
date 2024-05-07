<?php

declare(strict_types=1);

namespace App\UseCases\Message;

use App\Domain\Entities\Message;
use App\Domain\Repositories\MessageEntityRepositoryInterface;
use App\Domain\Repositories\NewsletterEntityRepositoryInterface;
use App\UseCases\DTO\Message\MessageCreateInputDto;
use App\UseCases\DTO\Message\MessageCreateOutputDto;

class MessageCreateUseCase
{
    public function __construct(
        protected MessageEntityRepositoryInterface $repository,
        protected NewsletterEntityRepositoryInterface $newsletterEntityRepository
    ) {
        $this->repository = $repository;
        $this->newsletterEntityRepository = $newsletterEntityRepository;
    }

    public function execute(MessageCreateInputDto $input): MessageCreateOutputDto
    {
        $messageEntity = Message::create(
            title: $input->title,
            message: $input->message,
            newsLetterId: $input->newsLetterId
        );

        $this->newsletterEntityRepository->findById($messageEntity->newsLetterId);
        $message  = $this->repository->insert($messageEntity);
        // Event disparar emails para RabbitMQ
        // Event(new EmailMessageCreated($message));
        return new MessageCreateOutputDto(
            id: $message->id,
            newsletter_id: $message->newsLetterId,
            title: $message->title,
            message: $message->message,
            created_at: $message->createdAt
        );
    }
}
