<?php

namespace App\Messenger\Handler;

use App\Messenger\Message\SayHelloMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SayHelloMessageHandler
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(SayHelloMessage $message)
    {
        $this->logger->info('Hello world!', ['message' => $message]);
    }
}