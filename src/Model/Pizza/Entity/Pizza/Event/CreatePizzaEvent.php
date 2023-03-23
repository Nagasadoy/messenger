<?php

namespace App\Model\Pizza\Entity\Pizza\Event;

class CreatePizzaEvent
{
    public readonly string $pizzaId;
    public readonly string $name;
    public readonly string $description;
    public readonly int $price;

    public function __construct(string $pizzaId, string $name, string $description, int $price)
    {
        $this->pizzaId = $pizzaId;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }
}