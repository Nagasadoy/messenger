<?php

namespace App\Services\RabbitMqNative;

use Faker\Factory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class RabbitMq
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private LoggerInterface $logger;

    private const QUEUE_NAME = 'new_queue';

    public function __construct(LoggerInterface $logger)
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(self::QUEUE_NAME);
        $this->logger = $logger;
    }

    public function sendMessage(string $message): void
    {
        $message = new AMQPMessage($message);
        $this->channel->basic_publish($message, '', self::QUEUE_NAME);
    }

    public function consume(): void
    {
        $callback = function (AMQPMessage $msg) {
            $this->logger->info($msg->body);
        };

        $this->channel->queue_declare(self::QUEUE_NAME);

        $this->channel->basic_consume(self::QUEUE_NAME, callback: $callback);

        while ($this->channel->is_open) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }

    public function sendInfo(): void
    {
        $faker = Factory::create();
        $messageInfo = new AMQPMessage($faker->word . ' info');
        $messageWarn = new AMQPMessage($faker->word . ' warn');
        $messageErr = new AMQPMessage($faker->word . ' err');


        $this->channel->queue_declare('queue_logs_ifo');
        $this->channel->queue_declare('queue_logs_warn');
        $this->channel->queue_declare('queue_logs_err');
        $this->channel->queue_declare('queue_logs_all');
        $this->channel->exchange_declare('logs', 'direct', auto_delete: false);

        $this->channel->queue_bind('queue_logs_ifo', 'logs', 'info');
        $this->channel->queue_bind('queue_logs_warn', 'logs', 'warn');
        $this->channel->queue_bind('queue_logs_err', 'logs', 'err');

        $this->channel->queue_bind('queue_logs_all', 'logs', 'err');
        $this->channel->queue_bind('queue_logs_all', 'logs', 'warn');
        $this->channel->queue_bind('queue_logs_all', 'logs', 'info');


        $this->channel->basic_publish($messageInfo, 'logs', 'info');
        $this->channel->basic_publish($messageWarn, 'logs', 'warn');
        $this->channel->basic_publish($messageErr, 'logs', 'err');
    }


}