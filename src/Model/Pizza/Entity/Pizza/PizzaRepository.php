<?php

namespace App\Model\Pizza\Entity\Pizza;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class PizzaRepository
{
    public function __construct(
        private readonly EntityRepository $repo,
        private readonly EntityManagerInterface $entityManager
    ) { }

    public function get(string $id): Pizza
    {
        $pizza = $this->repo->find($id);

        if (null === $pizza) {
            throw new EntityNotFoundException();
        }

        return $pizza;
    }

    public function add(Pizza $pizza): void
    {
        $this->entityManager->persist($pizza);
    }
}