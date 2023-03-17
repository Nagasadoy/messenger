<?php

namespace App\Model\Ingredient\UseCase\Create;

use App\Model\Flusher;
use App\Model\Ingredient\Entity\Ingredient\Ingredient;
use App\Model\Ingredient\Entity\Ingredient\IngredientRepository;

class Handler
{
    public function __construct(
        private readonly IngredientRepository $ingredientRepository,
        private readonly Flusher $flusher
    ) { }

    public function handle(CreateIngredientCommand $command): void
    {
        $name = $command->name;
        $ingredient = new Ingredient($name);
        if ($this->ingredientRepository->isAlreadyExists($name)) {
            throw new \DomainException('Ingredient already exists');
        }

        $this->ingredientRepository->add($ingredient);
        $this->flusher->flush();
    }
}