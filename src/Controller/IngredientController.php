<?php

namespace App\Controller;

use App\Attribute\FromRequest;
use App\Model\Ingredient\UseCase\Create\CreateIngredientCommand;
use App\Model\Ingredient\UseCase\Create\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    public function __construct(private readonly Handler $handler)
    {
    }

    #[Route('ingredient/create', name: 'ingredient_create', methods: ['POST'])]
    public function create(
        #[FromRequest] CreateIngredientCommand $command,
        Handler $handler
    ): Response
    {
        $handler->handle($command);
        return $this->json(['message' => 'ingredient created!']);
    }
}