<?php

namespace App\ReadModel\Pizza\DTO;

class ResponsePizzaWithIngredientsDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $price,
        public readonly array $ingredients
    )
    {
    }
}