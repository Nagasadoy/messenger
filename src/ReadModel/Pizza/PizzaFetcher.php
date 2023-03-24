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

    public function all(int $page, int $size, string $sort, string $direction): \Knp\Component\Pager\Pagination\PaginationInterface
    {
//        $qb = $this->connection->createQueryBuilder()
//            ->select(
//                'id',
//                'date',
//                'TRIM(CONCAT(name_first, \' \', name_last)) AS name',
//                'email',
//                'role',
//                'status'
//            )
//            ->from('user_users');

        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.name',
                'p.price',
                'i.name as ingredient_name'
            )
            ->from('pizza', 'p')
            ->join('p', 'pizza_ingredient', 'p_i', 'p.id=p_i.pizza_id')
            ->join('p_i', 'ingredient', 'i', 'p_i.ingredient_id=i.id');

//        if ($filter->name) {
//            $qb->andWhere($qb->expr()->like('LOWER(CONCAT(name_first, \' \', name_last))', ':name'));
//            $qb->setParameter(':name', '%' . mb_strtolower($filter->name) . '%');
//        }
//
//        if ($filter->email) {
//            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
//            $qb->setParameter(':email', '%' . mb_strtolower($filter->email) . '%');
//        }
//
//        if ($filter->status) {
//            $qb->andWhere('status = :status');
//            $qb->setParameter(':status', $filter->status);
//        }
//
//        if ($filter->role) {
//            $qb->andWhere('role = :role');
//            $qb->setParameter(':role', $filter->role);
//        }

        if (!\in_array($sort, ['name'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
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