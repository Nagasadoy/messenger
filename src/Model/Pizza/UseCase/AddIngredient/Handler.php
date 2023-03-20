<?php

namespace App\Model\Pizza\UseCase\AddIngredient;

use App\Model\Flusher;
use App\Model\Ingredient\Entity\Ingredient\IngredientRepository;
use App\Model\Pizza\Entity\Pizza\PizzaRepository;

class Handler
{
    public function __construct(
        private readonly Flusher $flusher,
        private readonly PizzaRepository $pizzaRepository,
        private readonly IngredientRepository $ingredientRepository
    ) { }

    public function handle(AddIngredientCommand $command): void
    {
        $pizzaId = $command->pizzaId;
        $ingredientId = $command->ingredientId;

        $pizza = $this->pizzaRepository->get($pizzaId);
        $ingredient = $this->ingredientRepository->get($ingredientId);

        $pizza->addIngredient($ingredient);
        $this->flusher->flush($pizza);
    }

}