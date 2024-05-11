<?php

namespace App\Console\Commands;

use App\Domain\Enum\TransactionStatus;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\TransferAprovedInputDto;
use App\UseCases\Transaction\TransferAprovedUseCase;
use App\UseCases\Transaction\TransferReprovedUseCase;
use Exception;
use Illuminate\Console\Command;

class RabbitMQCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumer RabbitMQ';

    public function __construct(
        private RabbitInterface $amqp,
        private TransferAprovedUseCase $transferAprovedUseCase,
        private TransferReprovedUseCase $transferReprovedUseCase
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $closure = function ($message) {
            $body = json_decode($message->body);
            try {

                if (isset($body->transactionType) && $body->transactionType === 'TRANSFER') {
                    if ($body->transactionStatus === TransactionStatus::APROVED->value) {
                        $this->transferAprovedUseCase->execute(
                            new TransferAprovedInputDto(
                                transactionId: $body->id,
                                payerId: $body->payerId,
                                payeeId: $body->payeeId,
                                value: $body->value
                            )
                        );
                    }

                    if ($body->transactionStatus === TransactionStatus::ERROR->value) {
                        $this->transferReprovedUseCase->execute(
                            new TransferAprovedInputDto(
                                transactionId: $body->id,
                                payerId: $body->payerId,
                                payeeId: $body->payeeId,
                                value: $body->value
                            )
                        );
                    }
                }
                $message->ack();
            } catch (Exception $e) {
                // Notificar e rtata o erro, enviando para uma fila de reprocessamento
            }
        };

        $this->amqp->consumer(
            queue: 'transferProcessed',
            exchange: 'transferProcessed',
            callback: $closure
        );

        return 0;
    }
}
