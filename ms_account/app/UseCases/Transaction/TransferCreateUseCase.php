<?php

declare(strict_types=1);

namespace App\UseCases\Transaction;

use App\Domain\Entities\Transaction;
use App\Domain\Enum\TransactionType;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Domain\Repositories\TransactionEntityRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Exceptions\BadRequestException;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\TransactionCreateInputDto;
use App\UseCases\DTO\Transaction\TransactionCreateOutputDto;
use Illuminate\Support\Facades\DB;

class TransferCreateUseCase
{
    public function __construct(
        protected TransactionEntityRepositoryInterface $repository,
        protected AccountEntityRepositoryInterface $accountEntityRepositoryInterface,
        protected RabbitInterface $rabbitMqService
    ) {
    }

    public function execute(TransactionCreateInputDto $input): TransactionCreateOutputDto
    {
        $transactionEntity = Transaction::create(
            transactionType: TransactionType::TRANSFER,
            payerId: new Uuid($input->payerId),
            payeeId: new Uuid($input->payeeId),
            value: (float)$input->value
        );

        $payer = $this->accountEntityRepositoryInterface->findById($transactionEntity->payerId());
        $this->accountEntityRepositoryInterface->findById($transactionEntity->payeeId());
        $self = $this;
        $transaction = DB::transaction(function () use($self, $payer, $transactionEntity) {
            $payer->makeTransfer($transactionEntity->value);
            $transaction = $self->repository->insert($transactionEntity);
            $self->accountEntityRepositoryInterface->update($payer);
            $self->rabbitMqService->producer("transferCreated", $transaction->toArray());
            return $transaction;
        });

        return new TransactionCreateOutputDto(
            id: $transaction->id(),
            transaction_type: $transaction->transactionType(),
            payer_id: $transaction->payerId(),
            payee_id: $transaction->payeeId(),
            value: $transaction->value,
            transaction_status: $transaction->transactionStatus(),
            created_at: $transaction->createdAt()
        );
    }
}
