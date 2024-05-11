<?php

namespace App\Console\Commands;

use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\ProcessTransferInputDto;
use App\UseCases\Transaction\ProcessTransferUseCase;
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
        private ProcessTransferUseCase $processTransferUseCase
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
                    $transferFake = $this->processTransferUseCase->execute(
                        new ProcessTransferInputDto(
                            id: $body->id,
                            transactionType: $body->transactionType,
                            payerId: $body->payerId,
                            payeeId: $body->payeeId,
                            value: $body->value,
                            transactionStatus: $body->transactionStatus
                        )
                    );
                }

                $this->amqp->producerWhileHaveRegister("transferProcessed", (array)$transferFake);
                $message->ack();
            } catch (Exception $e) {
                dd($e);
            }
        };

        $this->amqp->consumer(
            queue: 'transferCreated',
            exchange: 'transferCreated',
            callback: $closure
        );

        return 0;
    }
}
