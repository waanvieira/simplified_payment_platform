<?php

namespace App\UseCases\DTO\NewsLetter;

class NewsLetterCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $created_at
    ) {
    }
}
