<?php

namespace App\Model\Pizza\UseCase\AddIngredient;

use Symfony\Component\Uid\Uuid;

class AddIngredientCommand
{
    public string $pizzaId;
    public string $ingredientId;
}