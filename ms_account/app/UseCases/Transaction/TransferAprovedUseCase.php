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

class TransferAprovedUseCase
{
    public function __construct(
        protected TransactionEntityRepositoryInterface $repository,
        protected AccountEntityRepositoryInterface $accountEntityRepositoryInterface,
        protected RabbitInterface $rabbitMqService
    ) {
    }

    public function execute(TransferAprovedInputDto $input) : void
    {
        $transactionEntity = Transaction::restore(
            id: new Uuid($input->transactionId),
            transactionType: TransactionType::TRANSFER,
            payerId: new Uuid($input->payerId),
            payeeId: new Uuid($input->payeeId),
            value: (float)$input->value,
            transactionStatus: TransactionStatus::APROVED
        );
        $this->repository->findById($transactionEntity->id());
        $self = $this;
        $payee = $this->accountEntityRepositoryInterface->findById($transactionEntity->payeeId());
        DB::transaction(function () use($self, $payee, $transactionEntity) {
            $transactionEntity->paymentAproved();
            $self->repository->update($transactionEntity);
            $payee->receiveTransfer($transactionEntity->value);
            $self->accountEntityRepositoryInterface->update($payee);
            $self->rabbitMqService->producer("notifyTransaction", $transactionEntity->toArray());
        });
    }
}
