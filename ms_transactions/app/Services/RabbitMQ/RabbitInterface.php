<?php

namespace App\Services\RabbitMQ;

use Closure;

interface RabbitInterface
{
    public function producer(string $queue, array $payload, string $exchange = ''): void;

    public function producerInLote(string $queue, array $payload, int $registerNumber, int $currentRegisterNumber): void;

    public function producerFanout(string $queue, array $payload, string $exchange = ''): void;

    public function consumer(string $queue, string $exchange, Closure $callback): void;
}
