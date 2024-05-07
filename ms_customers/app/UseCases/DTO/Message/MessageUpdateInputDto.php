<?php

namespace App\UseCases\DTO\Message;

class MessageUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $message,
        public string $newsLetterId
    ) {
    }
}
