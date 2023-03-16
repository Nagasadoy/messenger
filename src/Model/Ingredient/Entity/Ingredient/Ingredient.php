<?php

namespace App\Model\Ingredient\Entity\Ingredient;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     */

    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;


    #[Orm\Column]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}