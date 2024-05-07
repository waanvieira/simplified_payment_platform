<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Traits\MethodsMagicsTrait;
use Ramsey\Uuid\Uuid;

class Message
{
    use MethodsMagicsTrait;

    private function __construct(
        protected ?string $id,
        protected string $title,
        protected string $message,
        protected ?string $newsLetterId = '',
        protected ?string $createdAt = ''
    ) {
    }

    public static function create(string $title, string $message, string $newsLetterId): self
    {
        $id = Uuid::uuid4()->toString();
        $dateNow = date('Y-m-d H:i:s');
        return new self(
            id: $id,
            title: $title,
            message: $message,
            newsLetterId: $newsLetterId,
            createdAt: $dateNow
        );
    }

    public static function restore(?string $id, string $title, string $message, string $newsLetterId, string $createdAt = ''): self
    {
        return new self(
            id: $id,
            title: $title,
            message: $message,
            newsLetterId: $newsLetterId,
            createdAt: $createdAt
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'newsLetterId' => $this->newsLetterId,
            'createdAt' => $this->createdAt,
        ];
    }

    public static function fromArray(
        array $data
    ): self {
        return new self(
            id: $data['id'],
            title: $data['title'],
            message: $data['message'],
            createdAt: null,
        );
    }

    public function update(string $title, string $message, string $newsLetterId): void
    {
        $this->title = $title;
        $this->message = $message;
        $this->newsLetterId = $newsLetterId;
    }
}
