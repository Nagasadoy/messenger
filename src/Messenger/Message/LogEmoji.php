<?php

namespace App\Messenger\Message;

class LogEmoji
{
    private int $emojiIndex;

    public function __construct(int $emojiIndex)
    {
        $this->emojiIndex = $emojiIndex;
    }

    public function getEmojiIndex(): int
    {
        return $this->emojiIndex;
    }
}