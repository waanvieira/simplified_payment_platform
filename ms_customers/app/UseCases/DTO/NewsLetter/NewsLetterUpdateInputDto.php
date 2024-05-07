<?php

namespace App\UseCases\DTO\NewsLetter;

class NewsLetterUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $email
    )
    {

    }
}
