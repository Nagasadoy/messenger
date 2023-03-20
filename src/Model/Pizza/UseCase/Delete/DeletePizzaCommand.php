<?php

namespace App\Model\Pizza\UseCase\Delete;

use Symfony\Component\Uid\Uuid;

class DeletePizzaCommand
{
    public Uuid $id;
}