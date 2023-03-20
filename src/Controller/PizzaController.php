<?php

namespace App\Controller;

use App\Attribute\FromRequest;
use App\Model\Pizza\UseCase\Create\CreatePizzaCommand;
use App\Model\Pizza\UseCase;
use App\ReadModel\Pizza\PizzaFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class PizzaController extends AbstractController
{
    private const PER_PAGE = 50;

    #[OA\Response(
        response: 200,
        description: 'Создает пиццу',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            example: [
                'name' => 'Pizza',
                'price' => 1200
            ]
        )
    )]
    #[Route('api/pizza/create', methods: ['POST'])]
    public function create(#[FromRequest] CreatePizzaCommand $command, UseCase\Create\Handler $handler): Response
    {
        $handler->handle($command);
        return $this->json(['message' => 'Pizza is created']);
    }

    #[Route('api/pizza/addIngredient')]
    public function addIngredient(
        #[FromRequest] UseCase\AddIngredient\AddIngredientCommand $command,
        UseCase\AddIngredient\Handler $handler
    ): Response
    {
        $handler->handle($command);
        return $this->json(['message' => 'Ингредиент добавлен!']);
    }

    #[Route('api/pizza')]
    public function getAllPizzas(Request $request, PizzaFetcher $fetcher): Response
    {
        $pagination = $fetcher->all(
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'name'),
            $request->query->get('direction', 'desc')
        );

        return $this->json([
           'pagination' => $pagination
        ]);
    }
}