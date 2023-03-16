<?php

namespace App\Model\Ingredient\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class CreateIngredientCommand
{
    #[Assert\Length(min: 2, minMessage: 'Название ингредиента должно быть минимум 2 символа!')]
    public string $name;
}