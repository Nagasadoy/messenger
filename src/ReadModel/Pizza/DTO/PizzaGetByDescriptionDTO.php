<?php

namespace App\ReadModel\Pizza\DTO;

class PizzaGetByDescriptionDTO
{
    public readonly string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }
}