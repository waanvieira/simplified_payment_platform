<?php

namespace App\Domain\ValueObjects;

use App\Exceptions\InvalidArgumentException;

class Email
{
    public function __construct(
        protected string $value
    ) {
        $this->validateEmail($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validateEmail(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email address is invalid.");
        }
    }
}
