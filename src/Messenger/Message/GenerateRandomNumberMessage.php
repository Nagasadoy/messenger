<?php

namespace App\Messenger\Message;

class GenerateRandomNumberMessage
{
    private int $randomInt;

    public function __construct()
    {
        $this->randomInt = rand(0, 1000);
    }

    public function getRandomInt(): int
    {
        return $this->randomInt;
    }

}