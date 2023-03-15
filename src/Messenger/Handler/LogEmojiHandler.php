<?php

namespace App\Messenger\Handler;


use App\Messenger\Message\LogEmoji;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LogEmojiHandler
{
    private static array $emojis = [
        '😀',
        '👻',
        '😈',
        '🎉',
        '😍'
    ];

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(LogEmoji $logEmoji)
    {
        $index = $logEmoji->getEmojiIndex();

        $emoji = self::$emojis[$index] ?? self::$emojis[0];

        $this->logger->info('Important message! ' . $emoji);
    }

}