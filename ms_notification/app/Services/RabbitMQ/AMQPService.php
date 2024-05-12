<?php

namespace App\Services\RabbitMQ;

use Closure;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPService implements RabbitInterface
{
    protected $connection = null;
    protected $arrayDataConnection;
    protected $channel = null;

    public function __construct()
    {
        $this->arrayDataConnection['host'] = env('RABBITMQ_HOST', 'rabbitmq');
        $this->arrayDataConnection['port'] = env('RABBITMQ_PORT', '5672');
        $this->arrayDataConnection['user'] = env('RABBITMQ_LOGIN', 'guest');
        $this->arrayDataConnection['password'] = env('RABBITMQ_PASSWORD', 'guest');
        $this->arrayDataConnection['vhost'] = env('RABBITMQ_VHOST', '/');
    }

    public function producer(string $queue, array $payload, string $exchange = ''): void
    {
        $this->connect();
        (bool)$durable = true;
        $this->channel->queue_declare($queue, $durable, false, false, false);
        $this->channel->exchange_declare($queue, 'direct', false, true, false);
        // Por hora não estamos usando bind no rabbit, avaliar quadno tiver essa mudança
        // $this->channel->queue_bind($queue, $exchange);

        $message = new AMQPMessage(
            json_encode($payload),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($message, $exchange, $queue);
        $this->closeChannel();
        $this->closeConnection();
    }

    public function producerWhileHaveRegister(string $queue, array $payload, string $exchange = ''): void
    {
        $this->connect();
        (bool)$durable = true;
        $this->channel->queue_declare($queue, $durable, false, false, false);
        $this->channel->exchange_declare($queue, 'direct', false, true, false);
        $message = new AMQPMessage(
            json_encode($payload),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($message, $exchange, $queue);
    }

    public function producerInLote(string $queue, array $payload, int $registerNumber, int $currentRegisterNumber): void
    {
        $this->connect();
        $this->channel->queue_declare($queue, false, false, false, false);
        $this->channel->exchange_declare($queue, AMQPExchangeType::DIRECT, false, true, false);

        $message = new AMQPMessage(
            json_encode($payload),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($message, '', $queue);

        if ($registerNumber === $currentRegisterNumber) {
            $this->closeChannel();
            $this->closeConnection();
        }
    }

    public function producerFanout(string $queue, array $payload, string $exchange = ''): void
    {
        $this->connect();

        $this->channel->exchange_declare(
            exchange: $exchange,
            type: AMQPExchangeType::FANOUT,
            passive: false,
            durable: true,
            auto_delete: false
        );

        $message = new AMQPMessage(json_encode($payload), [
            'content_type' => 'text/plain',
        ]);

        $this->channel->basic_publish($message, $exchange);

        $this->closeChannel();
        $this->closeConnection();
    }

    public function consumer(string $queue, string $exchange, Closure $callback): void
    {
        $this->connect();

        $this->channel->queue_declare(
            queue: $queue,
            durable: false,
            auto_delete: false
        );

        // Por hora não estamos usando bind no rabbit, avaliar quadno tiver essa mudança
        // $this->channel->queue_bind(
        // queue: $queue,
        // exchange: $exchange,
        // routing_key: config('microservices.queue_name')
        // );

        $this->channel->basic_consume(
            queue: $queue,
            callback: $callback
        );
        // Comentado apenas por enquanto que esse MS não tem filas para consumir, para não travar os testes unitários
        while ($this->channel->is_consuming()) {
            // try {
                $this->channel->wait();
            // } catch (Exception $e) {
                // dump($body);
                // dump($e);
                // continue;
            // }
        }

        $this->closeChannel();
        $this->closeConnection();
    }

    private function connect(): void
    {
        if ($this->connection) {
            return;
        }

        if (env('RABBITMQ_SCHEME') === "amqps") {
            $this->sslConnection();
        } else {
            $this->streamConnection();
        }

        $this->channel = $this->connection->channel();
    }

    private function closeChannel(): void
    {
        $this->channel->close();
    }

    private function closeConnection(): void
    {
        $this->connection->close();
    }

    private function sslConnection()
    {
        if (!defined('CERTS_PATH')) {
            define('CERTS_PATH', realpath(__DIR__ . "/../../../cert-ssl/"));
        }

        $ssl_opts = array(
            'ssl_version' => CERTS_PATH . 'tlsv1.2',
            'capath' => CERTS_PATH,
            'cafile' => getenv('RABBITMQ_SSL_CA_CERTIFICATE'),
            'verify_peer' => false,
            'verify_peer_name' => false,
        );

        $options = array(
            'login_method' => env('RABBITMQ_LM', 'PLAIN'),
            'insist' => env('RABBITMQ_INSIST', false),
            'connection_timeout' => env('RABBITMQ_CT', '6000.0'),
            'read_write_timeout' => env('RABBITMQ_RWT', '6000.0'),
            'heartbeat' => env('RABBITMQ_HEARTBEAT', '15'),
        );

        $this->connection = new AMQPSSLConnection(
            $this->arrayDataConnection['host'],
            $this->arrayDataConnection['port'],
            $this->arrayDataConnection['user'],
            $this->arrayDataConnection['password'],
            $this->arrayDataConnection['vhost'],
            $ssl_opts,
            $options
        );
    }

    public function streamConnection()
    {
        // dd($this->arrayDataConnection);
        $this->connection = new AMQPStreamConnection(
            host: $this->arrayDataConnection['host'],
            port: $this->arrayDataConnection['port'],
            user: $this->arrayDataConnection['user'],
            password: $this->arrayDataConnection['password'],
            vhost: $this->arrayDataConnection['vhost'],
        );
    }
}
