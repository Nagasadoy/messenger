<?php

namespace App\Controller;

use App\Attribute\FromRequest;
use App\Model\Ingredient\UseCase\Create\CreateIngredientCommand;
use App\Model\Ingredient\UseCase;
use App\Model\Ingredient\UseCase\Delete\DeleteIngredientCommand;
use App\Model\Ingredient\UseCase\Edit\EditIngredientCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{

    #[Route('api/ingredient/create', name: 'ingredient_create', methods: ['POST'])]
    public function create(
        #[FromRequest] CreateIngredientCommand $command,
        UseCase\Create\Handler $handler
    ): Response
    {
        $handler->handle($command);
        return $this->json(['message' => 'ingredient created!']);
    }

    #[Route('api/ingredient/delete/{id}', name: 'ingredient_delete', methods: ['DELETE'])]
    public function delete(string $id, UseCase\Delete\Handler $handler): Response
    {
        $command = new DeleteIngredientCommand();
        $command->id = $id;

        $handler->handle($command);

        return $this->json(['message' => 'ingredient with id=' . $id . ' removed']);
    }

    #[Route('api/ingredient/edit', name: 'ingredient_edit', methods: ['POST'])]
    public function edit(
        #[FromRequest] EditIngredientCommand $command,
        UseCase\Edit\Handler $handler
    ): Response
    {
        $handler->handle($command);
        return $this->json(['message' => 'ingredient with id=' . $command->id . ' updated']);
    }
}