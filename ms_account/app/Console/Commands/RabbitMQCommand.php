<?php

namespace App\Console\Commands;

use App\Domain\Enum\TransactionStatus;
use App\Domain\Enum\TransactionType;
use App\Exceptions\NotFoundException;
use App\Services\AMQP\AMQPInterface;
use App\Services\RabbitMQ\RabbitInterface;
use App\UseCases\DTO\Transaction\TransferAprovedInputDto;
use App\UseCases\Transaction\TransferAprovedUseCase;
use App\UseCases\Transaction\TransferReprovedUseCase;
use Core\UseCase\Video\ChangeEncoded\ChangeEncodedPathVideo;
use Core\UseCase\Video\ChangeEncoded\DTO\ChangeEncodedVideoDTO;
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

                if (isset($body->transactionType) && $body->TRANSFER === '') {
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
            } catch (Exception $e) {
                if ($e instanceof NotFoundException) {
                    // Notificar erro
                }
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
