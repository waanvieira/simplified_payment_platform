<?php

namespace App\UseCases\DTO\Message;

class MessageCreateInputDto
{
    public function __construct(
        public string $title,
        public string $message,
        public string $newsLetterId
    ) {
    }
}
