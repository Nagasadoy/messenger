<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

use App\Event\Dispatcher\Message\Message;
use App\Model\EventDispatcher;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventDispatcher implements EventDispatcher
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {

            $message = new Message($event);

//            $envelope = new Envelope($message,[
//                new AmqpStamp('hhh')
//            ]);

            $this->bus->dispatch($message);
        }
    }
}
