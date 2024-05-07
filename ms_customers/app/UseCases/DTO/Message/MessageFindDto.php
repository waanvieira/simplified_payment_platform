<?php

namespace App\UseCases\DTO\Message;

class MessageFindDto
{
    public function __construct(
        public string $id
    ) {
    }
}
