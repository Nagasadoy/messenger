<?php

namespace App\ReadModel\Pizza\DTO;

class ResponsePizzaDTO
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $description;
    public readonly int $price;

    public function __construct(string $id, string $name, string $description, int $price)
    {
        $this->description = $description;
        $this->name = $name;
        $this->price = $price;
        $this->id = $id;
    }
}