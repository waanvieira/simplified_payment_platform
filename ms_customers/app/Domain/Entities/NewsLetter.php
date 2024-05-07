<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Uuid;
use App\Traits\MethodsMagicsTrait;
use DateTime;
use Ramsey\Uuid\Uuid as Ramsey;

class NewsLetter
{
    use MethodsMagicsTrait;

    private function __construct(
        protected Uuid|string $id,
        protected string $name,
        protected string $description,
        protected DateTime|string $createdAt = '',
    ) {
    }

    public static function create(string $name, string $description): self
    {
        return new self(
            id: Uuid::random(),
            name: $name,
            description: $description,
            createdAt: date('Y-m-d H:i:s')
        );
    }

    public static function restore(string $id, string $name, string $description, string $createdAt = ''): self
    {
        return new self(
            id: new Uuid($id),
            name: $name,
            description: $description,
            createdAt: $createdAt
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'createdAt' => $this->createdAt,
        ];
    }

    public static function fromArray(
        array $data
    ): self {
        return new self(
            id: $data['id'],
            name: $data['name'],
            description: $data['description'],
            // createdAt: null,
        );
    }

    public function update(string $name, string $description): void
    {
        $this->name = $name;
        $this->description = $description;
    }
}
