<?php

declare(strict_types=1);

namespace App\UseCases\Transaction;

use App\Gateway\ExternalGatewayPaymentService;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\ProcessTransferInputDto;
use App\UseCases\DTO\Transaction\ProcessTransferOutputDto;

class ProcessTransferUseCase
{
    public function __construct(
        protected ExternalGatewayPaymentService $service,
        protected RabbitInterface $rabbitMqService
    ) {
    }

    public function execute(ProcessTransferInputDto $input) : ProcessTransferOutputDto
    {
        $this->service->processPayment((array)$input);
        $arrayStatus = array("APROVED", "REPROVED");
        $rand_keys = array_rand($arrayStatus);
        $transferFakeAproved = [
            "id" => $input->id,
            "transactionType" => $input->transactionType,
            "payerId" => $input->payerId,
            "payeeId" => $input->payeeId,
            "value" => $input->value,
            "transactionStatus" => $arrayStatus[$rand_keys],
        ];

        return new ProcessTransferOutputDto(
            id: $transferFakeAproved['id'],
            transactionType: $transferFakeAproved['transactionType'],
            payerId: $transferFakeAproved['payerId'],
            payeeId: $transferFakeAproved['payeeId'],
            value: $transferFakeAproved['value'],
            transactionStatus: $transferFakeAproved['transactionStatus'],
        );
    }
}
