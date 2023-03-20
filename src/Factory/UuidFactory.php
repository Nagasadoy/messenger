<?php

namespace App\Factory;

use Symfony\Component\Uid\Uuid;

class UuidFactory
{
    public static function generateNew(): Uuid
    {
        return Uuid::v4();
    }
}