<?php

namespace App\Model\Ingredient\UseCase\Delete;

use App\Model\Flusher;
use App\Model\Ingredient\Entity\Ingredient\IngredientRepository;
use Symfony\Component\Uid\Uuid;

class Handler
{
    public function __construct(
        private readonly IngredientRepository $ingredientRepository,
        private readonly Flusher $flusher
    ) { }

    public function handle(DeleteIngredientCommand $command): void
    {
        $id = $command->id;
        $this->ingredientRepository->remove(Uuid::fromString($id));
        $this->flusher->flush();
    }
}