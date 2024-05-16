<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\EntityValidationException;
use DateTime;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Transaction extends Entity
{
    public function __construct(
        protected Uuid $id,
        protected TransactionType $transactionType,
        protected Uuid $payerId,
        protected Uuid $payeeId,
        protected float $value,
        protected TransactionStatus|string $transactionStatus = '',
        protected DateTime|string $createdAt = '',
        protected DateTime|string $confirmationAt = ''
    ) {
        $this->createdAt = empty($this->createdAt) ? new DateTime() : $this->createdAt;
        $this->transactionStatus = TransactionStatus::PROCESSING;
    }

    public static function create(TransactionType $transactionType, Uuid $payerId, Uuid $payeeId, float $value): self
    {
        $id = RamseyUuid::uuid4()->toString();

        return new self(
            id: new Uuid($id),
            transactionType: $transactionType,
            payerId: $payerId,
            payeeId: $payeeId,
            value: $value
        );
    }

    public static function restore(Uuid $id, TransactionType $transactionType, Uuid $payerId, Uuid $payeeId, float $value, TransactionStatus $transactionStatus, DateTime|string $createdAt = '', DateTime|string $confirmationAt = ''): self
    {
        return new self(
            id: $id,
            transactionType: $transactionType,
            payerId: $payerId,
            payeeId: $payeeId,
            value: $value,
            transactionStatus: $transactionStatus,
            createdAt: $createdAt,
            confirmationAt: $confirmationAt
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'transactionType' => $this->transactionType(),
            'payerId' => $this->payerId(),
            'payeeId' => $this->payeeId(),
            'value' => $this->value,
            'transactionStatus' => $this->transactionStatus(),
            'createdAt' => $this->createdAt(),
        ];
    }

    public function paymentAproved(): void
    {
        if ($this->transactionStatus === TransactionStatus::APROVED) {
            throw new EntityValidationException('payment already approved');
        }

        $this->confirmationAt = new DateTime();
        $this->transactionStatus = TransactionStatus::APROVED;
    }

    public function paymentReproved(): void
    {
        if ($this->transactionStatus === TransactionStatus::APROVED) {
            throw new EntityValidationException('payment already approved');
        }

        $this->confirmationAt = new DateTime();
        $this->transactionStatus = TransactionStatus::ERROR;
    }

    public function payerId(): string
    {
        return (string) $this->payerId;
    }

    public function payeeId(): string
    {
        return (string) $this->payeeId;
    }

    public function transactionType(): string
    {
        return (string) $this->transactionType->value;
    }

    public function transactionStatus(): string
    {
        return (string) $this->transactionStatus->value;
    }

    public function confirmationAt(): string
    {
        return (string) $this->confirmationAt->format('Y-m-d H:i:s');
    }
}
