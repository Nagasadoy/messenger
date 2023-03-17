<?php

namespace App\Model\Ingredient\UseCase\Edit;

use App\Model\Flusher;
use App\Model\Ingredient\Entity\Ingredient\IngredientRepository;

class Handler
{

    public function __construct(
        private readonly IngredientRepository $ingredientRepository,
        private readonly Flusher $flusher
    )
    {
    }

    public function handle(EditIngredientCommand $command): void
    {
        $ingredient = $this->ingredientRepository->get($command->id);
        $ingredient->setName($command->name);
        $this->flusher->flush();
    }

}