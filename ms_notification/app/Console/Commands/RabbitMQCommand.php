<?php

namespace App\Console\Commands;

use App\Domain\Enum\TransactionStatus;
use App\Gateway\SendMailGatewayService;
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
        protected SendMailGatewayService $sendMailGatewayService,
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
                $this->sendMailGatewayService->sendMail((array)$body);
                $message->ack();
            } catch (Exception $e) {
                // Tratar erro
            }
        };

        $this->amqp->consumer(
            queue: 'notifyTransaction',
            exchange: 'notifyTransaction',
            callback: $closure
        );

        return 0;
    }
}
