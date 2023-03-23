<?php

namespace App\Model\Pizza\UseCase\Create;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePizzaCommand
{
    #[Assert\Length(min: 2, max: 20,
        minMessage: 'Название не должно быть короче 2 символов!',
        maxMessage: 'Название не должно быть длиннее 20 символов!'
    )]
    public string $name;

    #[Assert\Positive]
    public int $price;

    public string $description;

}