<?php

namespace App\UseCases\DTO\NewsLetter;

class NewsLetterUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $created_at = '',
        public string $updated_at = '',
    ) {
    }
}
