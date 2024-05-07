<?php

namespace App\UseCases\DTO\Message;

class MessageUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $newsletter_id,
        public string $title,
        public string $message,
        public string $created_at = '',
        public string $updated_at = ''
    ) {
    }
}
