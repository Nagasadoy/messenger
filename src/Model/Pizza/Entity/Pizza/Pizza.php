<?php

namespace App\Model\Pizza\Entity\Pizza;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use App\Model\Ingredient\Entity\Ingredient\Ingredient;
use App\Model\Pizza\Entity\Pizza\Event\AddIngredientEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Pizza implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(unique: true)]
    private string $name;

    #[ORM\Column]
    private int $price;

    #[ORM\Column]
    private string $description;

    #[ORM\ManyToMany(targetEntity: Ingredient::class)]
    public Collection $ingredients;

    public function __construct(Uuid $id, string $name, int $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;

        $this->ingredients = new ArrayCollection();
    }

    public function addIngredient(Ingredient $ingredient): void
    {
        $this->ingredients->add($ingredient);
        $this->recordEvent(new AddIngredientEvent($this->id, $ingredient->getId()));
    }

    public function removeIngredient(Ingredient $ingredient): void
    {
        $this->ingredients->remove($ingredient);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

}