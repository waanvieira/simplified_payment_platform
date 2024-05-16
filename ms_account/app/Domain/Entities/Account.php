<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Validation\DomainValidation;
use App\Domain\ValueObjects\CpfCnpj;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\EntityValidationException;
use DateTime;
use Ramsey\Uuid\Uuid as RamseyUuid;
// use Decimal\Decimal;
class Account extends Entity
{
    public function __construct(
        protected Uuid $id,
        protected string $name,
        protected CpfCnpj|string $cpfCnpj,
        protected Email|string $email,
        protected string $password = '',
        protected ?float $balance = 0,
        protected bool|string $shopkeeper = '',
        protected DateTime|string $createdAt = '',
        protected array $transactionsId = [],
    ) {
        $this->createdAt = empty($this->createdAt) ? new DateTime() : $this->createdAt;
        $this->shopkeeper = $this->isShopkeeper();
    }

    public static function create(string $name, string $cpfCnpj, string $email, string $password, ?float $balance = 0): self
    {
        $id = RamseyUuid::uuid4()->toString();
        return new self(
            id: new Uuid($id),
            name: $name,
            cpfCnpj: new CpfCnpj($cpfCnpj),
            email: new Email($email),
            password: $password,
            balance: $balance
        );
    }

    public static function restore(UUid $id, string $name, CpfCnpj $cpfCnpj, Email $email, bool $shopkeeper = false, ?float $balance = 0, DateTime|string $createdAt = ''): self
    {
        return new self(
            id: $id,
            name: $name,
            cpfCnpj: $cpfCnpj,
            email: $email,
            password: '',
            shopkeeper: $shopkeeper,
            balance: $balance,
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
            'balance' => $this->balance,
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

    public function makeTransfer(float $value)
    {
        if ($this->isShopkeeper()) {
            throw new EntityValidationException("Shopkeeper can't make transfer");
        }

        if ($this->balance < 0.01 || $this->balance < $value) {
            throw new EntityValidationException('balance unavailable to carry out transaction');
        }

        $this->balance -= $value;
        // $balance = new Decimal((string)$this->balance);
        // $value = new Decimal((string)$value);
        // $result = $balance - $value;
        // $this->balance = $result->toFloat();

    }

    public function receiveTransfer(float $value)
    {
        // $balance = new Decimal((string)$this->balance);
        // $value = new Decimal((string)$value);
        // $result = $balance += $value;
        // $this->balance = $result->toFloat();
        $this->balance += $value;
    }

    public function transferReprovedEstimateValue(float $value)
    {
        // $balance = new Decimal((string)$this->balance);
        // $value = new Decimal((string)$value);
        // $result = $balance += $value;
        // $this->balance = $result->toFloat();
        $this->balance += $value;
    }

    public function addTransaction(string $transactionId)
    {
        array_push($this->transactionsId, $transactionId);
    }

    public function removeTransaction(string $transactionId)
    {
        unset($this->transactionsId[array_search($transactionId, $this->transactionsId)]);
    }

    protected function validation()
    {
        DomainValidation::strNotNullAndMinAndMaxLength($this->name);
        DomainValidation::strNotNullAndMinAndMaxLength($this->cpfCnpj);
        DomainValidation::strNotNullAndMinAndMaxLength($this->email);
    }
}
