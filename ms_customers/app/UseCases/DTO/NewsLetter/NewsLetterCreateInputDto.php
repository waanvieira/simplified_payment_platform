<?php

namespace App\UseCases\DTO\NewsLetter;

class NewsLetterCreateInputDto
{
    public function __construct(
        public string $name,
        public string $description,
        public string $email
    ) {
    }
}
