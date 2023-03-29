<?php

namespace App\Services\RabbitMqNative;

use Faker\Factory;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class RabbitMq
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private LoggerInterface $logger;

    private array $colors = ['red', 'yellow', 'black', 'blue', 'green', 'white'];
    private array $brands = ['BMW', 'Toyota', 'Mercedes', 'Audi', 'Lada', 'Nissan'];

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


        $this->channel->queue_declare('queue_logs_ifo', auto_delete: false);
        $this->channel->queue_declare('queue_logs_warn', auto_delete: false);
        $this->channel->queue_declare('queue_logs_err', auto_delete: false);
        $this->channel->queue_declare('queue_logs_all', auto_delete: false);
        $this->channel->exchange_declare('logs', 'direct', auto_delete: false);

        // в каждую очередь свои виды сообщений
        $this->channel->queue_bind('queue_logs_ifo', 'logs', 'info');
        $this->channel->queue_bind('queue_logs_warn', 'logs', 'warn');
        $this->channel->queue_bind('queue_logs_err', 'logs', 'err');

        // в очередь queue_logs_all все виды сообщений
        $this->channel->queue_bind('queue_logs_all', 'logs', 'info');
        $this->channel->queue_bind('queue_logs_all', 'logs', 'warn');
        $this->channel->queue_bind('queue_logs_all', 'logs', 'err');

        $this->channel->basic_publish($messageInfo, 'logs', 'info');
        $this->channel->basic_publish($messageWarn, 'logs', 'warn');
        $this->channel->basic_publish($messageErr, 'logs', 'err');
    }

    public function sendTopic(): void
    {
        $this->channel->exchange_declare('brand_colors', 'topic', auto_delete: false);

        $this->channel->queue_declare('queue_red', auto_delete: false);
        $this->channel->queue_declare('queue_bmw', auto_delete: false);
        $this->channel->queue_declare('queue_13', auto_delete: false);
        $this->channel->queue_declare('queue_not_13', auto_delete: false);

        $this->channel->queue_bind('queue_red', 'brand_colors', '*.red.#');
        $this->channel->queue_bind('queue_bmw', 'brand_colors', 'BMW.#');
        $this->channel->queue_bind('queue_13', 'brand_colors', '*.*.13');
        $this->channel->queue_bind('queue_not_13', 'brand_colors', '*.*');

        for ($i = 0; $i < 100; $i++) {

            $index = array_rand($this->brands);
            $brand = $this->brands[$index];
            $index = array_rand($this->colors);
            $color = $this->colors[$index];

            $routingKey = "$brand.$color";

            if ($i % 13 === 0) {
                $routingKey .= ".13";
            }

            $messageInfo = new AMQPMessage($brand . ' ' . $color);
            $this->channel->basic_publish($messageInfo, 'brand_colors', $routingKey);
        }
    }

    public function sendFanout(): void
    {
        $this->channel->exchange_declare('my_fanout_exchange', 'fanout', auto_delete: false);

        $this->channel->queue_declare('a', auto_delete: false);
        $this->channel->queue_declare('b', auto_delete: false);
        $this->channel->queue_declare('c', auto_delete: false);

        $this->channel->queue_bind('a', 'my_fanout_exchange');
//        $this->channel->queue_bind('b', 'my_fanout_exchange');

        for ($i = 0; $i < 10; $i++) {
            $messageInfo = new AMQPMessage('message');
            $this->channel->basic_publish($messageInfo, 'my_fanout_exchange');
        }
    }

    public function consumeTopic(): void
    {
        $this->channel->queue_declare('queue_red', auto_delete: false);
        $this->channel->queue_declare('queue_bmw', auto_delete: false);
        $this->channel->queue_declare('queue_13', auto_delete: false);
        $this->channel->queue_declare('queue_not_13', auto_delete: false);

        $callback = function ($msg) {
            $this->logger->info('[x] Received ' . $msg->body);
        };

        $this->channel->basic_consume('queue_red', '', false, true, false, false, $callback);
        $this->channel->basic_consume('queue_bmw', '', false, true, false, false, $callback);
        $this->channel->basic_consume('queue_13', '', false, true, false, false, $callback);
        $this->channel->basic_consume('queue_not_13', '', false, true, false, false, $callback);

        while ($this->channel->is_open) {
            try {
                $this->channel->wait();
            } catch (AMQPTimeoutException|AMQPRuntimeException $ex) {
                break;
            }
        }
    }

    public function consumeFanout(): void
    {
        $this->channel->queue_declare('a', auto_delete: false);
        $this->channel->queue_declare('b', auto_delete: false);
        $this->channel->queue_declare('c', auto_delete: false);

        $callback = function ($msg) {
            $this->logger->info('[x] Received ' . $msg->body);
        };

        $this->channel->basic_consume('a', '', false, true, false, false, $callback);
        $this->channel->basic_consume('b', '', false, true, false, false, $callback);
        $this->channel->basic_consume('c', '', false, true, false, false, $callback);

        while ($this->channel->is_open) {
            try {
                $this->channel->wait();
            } catch (AMQPTimeoutException|AMQPRuntimeException $ex) {
                break;
            }
        }
    }


    public function consumeDirect(): void
    {
        $this->channel->queue_declare('queue_logs_all', false, false, false, false);
        $this->channel->queue_declare('queue_logs_ifo', false, false, false, false);
        $this->channel->queue_declare('queue_logs_warn', false, false, false, false);
        $this->channel->queue_declare('queue_logs_err', false, false, false, false);

        $callback = function ($msg) {
            $this->logger->info('[x] Received ' . $msg->body);
        };

        $this->channel->basic_consume('queue_logs_all', '', false, true, false, false, $callback);

        $this->channel->basic_consume('queue_logs_ifo', '', false, true, false, false, $callback);
        $this->channel->basic_consume('queue_logs_warn', '', false, true, false, false, $callback);
        $this->channel->basic_consume('queue_logs_err', '', false, true, false, false, $callback);

        while ($this->channel->is_open) {
            try {
                $this->channel->wait();
            } catch (AMQPTimeoutException|AMQPRuntimeException $ex) {
                break;
            }
        }
    }


}