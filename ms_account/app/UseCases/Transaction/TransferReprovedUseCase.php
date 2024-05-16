<?php

declare(strict_types=1);

namespace App\UseCases\Transaction;

use App\Domain\Entities\Transaction;
use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Domain\Repositories\AccountEntityRepositoryInterface;
use App\Domain\Repositories\TransactionEntityRepositoryInterface;
use App\Domain\ValueObjects\Uuid;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\TransferAprovedInputDto;
use Illuminate\Support\Facades\DB;

class TransferReprovedUseCase
{
    public function __construct(
        protected TransactionEntityRepositoryInterface $repository,
        protected AccountEntityRepositoryInterface $accountEntityRepositoryInterface,
        protected RabbitInterface $rabbitMqService
    ) {
    }

    public function execute(TransferAprovedInputDto $input): void
    {
        $transactionEntity = Transaction::restore(
            id: new Uuid($input->transactionId),
            transactionType: TransactionType::TRANSFER,
            payerId: new Uuid($input->payerId),
            payeeId: new Uuid($input->payeeId),
            value: (float) $input->value,
            transactionStatus: TransactionStatus::ERROR
        );

        $this->repository->findById($transactionEntity->id());
        $self = $this;
        $payer = $this->accountEntityRepositoryInterface->findById($transactionEntity->payerId());
        DB::transaction(function () use ($self, $payer, $transactionEntity) {
            $transactionEntity->paymentReproved();
            $self->repository->update($transactionEntity);
            $payer->transferReprovedEstimateValue($transactionEntity->value);
            $self->accountEntityRepositoryInterface->update($payer);
            $self->rabbitMqService->producer('notifyTransaction', $transactionEntity->toArray());
        });
    }
}
