<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\CpfCnpj;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Uuid;
use App\Traits\MethodsMagicsTrait;
use DateTime;
use Ramsey\Uuid\Uuid as RamseyUuid;

class User extends Entity
{
    public function __construct(
        protected Uuid|string $id,
        protected string $name,
        protected CpfCnpj|string $cpfCnpj,
        protected Email|string $email,
        protected string $password = '',
        protected bool|string $shopkeeper = '',
        protected DateTime|string $createdAt = ''
    ) {
        $this->createdAt = empty($this->createdAt) ? new DateTime() : $this->createdAt;
        $this->shopkeeper = $this->isShopkeeper();
    }

    public static function create(string $name, string $cpfCnpj, string $email, string $password): self
    {
        $id = RamseyUuid::uuid4()->toString();
        return new self(
            id: new Uuid($id),
            name: $name,
            cpfCnpj: new CpfCnpj($cpfCnpj),
            email: new Email($email),
            password: $password,
        );
    }

    public static function restore(?string $id, string $name, string $cpfCnpj, string $email, bool $shopkeeper = false, string $createdAt = ''): self
    {
        return new self(
            id: $id,
            name: $name,
            cpfCnpj: $cpfCnpj,
            email: $email,
            password: '',
            shopkeeper: $shopkeeper,
            createdAt: $createdAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpfCnpj' => $this->cpfCnpj,
            'email' => $this->email,
            'shopkeeper' => $this->shopkeeper,
            'createdAt' => $this->createdAt
        ];
    }

    public function isShopkeeper(): bool
    {
        if (strlen($this->cpfCnpj()) > 14) {
            return $this->shopkeeper = true;
        }

        return $this->shopkeeper = false;
    }

    public function cpfCnpj(): string
    {
        return (string) $this->cpfCnpj;
    }

    public function email(): string
    {
        return (string) $this->email;
    }
}
