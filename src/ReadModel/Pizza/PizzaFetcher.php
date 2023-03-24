<?php

namespace App\ReadModel\Pizza;

use App\Model\Pizza\Entity\Pizza\Pizza;
use App\Model\User\Entity\User\User;
use App\ReadModel\Pizza\DTO\ResponsePizzaDTO;
use App\Services\ElasticSearch;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;

class PizzaFetcher
{
    private EntityRepository $repository;

    public function __construct(
        private readonly Connection             $connection,
        private readonly EntityManagerInterface $em,
        private readonly PaginatorInterface     $paginator,
        private readonly ElasticSearch          $elasticSearch
    )
    {
        $this->repository = $em->getRepository(User::class);
    }

    public function all(int $page, int $size, string $sort, string $direction): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.name',
                'p.price',
                'p.description',
                'i.name as ingredient_name',
                'i.id as ingredient_id'
            )
            ->from('pizza', 'p')
            ->leftJoin('p', 'pizza_ingredient', 'p_i', 'p.id=p_i.pizza_id')
            ->leftJoin('p_i', 'ingredient', 'i', 'p_i.ingredient_id=i.id');

        if (!\in_array($sort, ['name'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        $pagination = $this->paginator->paginate($qb, $page, $size);

        $pizzas = [];

        foreach ($pagination as $item) {
            $id = $item['id'];
            $pizzaName = $item['name'];
            $description = $item['description'];
            $ingredientId = $item['ingredient_id'];
            $ingredientName = $item['ingredient_name'];


            if (array_key_exists($id, $pizzas)) {
                $pizzas[$id]['ingredients'][] = [
                    'id' => $ingredientId,
                    'name' => $ingredientName
                ];
            } else {
                $pizzas[$id] = [
                    'id' => $id,
                    'name' => $pizzaName,
                    'description' => $description,
                    'ingredients' => [[
                        'id' => $ingredientId,
                        'name' => $ingredientName
                    ]]
                ];
            }
        }
        return array_values($pizzas);
    }

    public function getByDescription(string $description): array
    {
        $pizzas = $this->elasticSearch->search(Pizza::PIZZA_INDEX, [
            'match_phrase_prefix' => [
                'description' => $description
            ]
        ]);

        $response = [];

        foreach ($pizzas as $pizza) {
            $pizza = $pizza['_source'];
            $pizzaResponse = new ResponsePizzaDTO(
                $pizza['id'],
                $pizza['name'],
                $pizza['description'],
                $pizza['price']
            );
            $response[] = $pizzaResponse;
        }

        return $response;
    }
}