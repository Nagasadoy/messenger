<?php

namespace App\Model\Pizza\Entity\Pizza\Event;

class AddIngredientEvent
{
    public readonly string $ingredientId;

    public readonly string $pizzaId;

    public function __construct(string $pizzaId, string $ingredientId)
    {
        $this->pizzaId = $pizzaId;
        $this->ingredientId = $ingredientId;
    }
}