<?php

namespace App\Model\Ingredient\Entity\Ingredient;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class IngredientRepository
{
    private EntityRepository $repo;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repo = $entityManager->getRepository(Ingredient::class);
        $this->entityManager = $entityManager;
//        $this->repo = $this->entityManager->getRepository(Ingredient::class);
    }

    public function add(Ingredient $ingredient): void
    {
        $this->entityManager->persist($ingredient);
    }

    public function get(string $id): Ingredient
    {
        $ingredient = $this->repo->find($id);

        if (null === $ingredient) {
            throw new EntityNotFoundException();
        }

        return $ingredient;
    }

    public function isAlreadyExists(string $name): bool
    {
        $ingredient = $this->repo->findOneBy(['name' => $name]);

        return $ingredient !== null;
    }
}