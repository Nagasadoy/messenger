<?php

namespace App\Model\Pizza\Entity\Pizza;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use App\Model\Ingredient\Entity\Ingredient\Ingredient;
use App\Model\Pizza\Entity\Pizza\Event\AddIngredientEvent;
use App\Model\Pizza\Entity\Pizza\Event\CreatePizzaEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Pizza implements AggregateRoot
{
    use EventsTrait;

    public const PIZZA_INDEX = 'pizza_index'; // индекс для elastic search

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

    public function __construct(Uuid $id, string $name, int $price, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;

        $this->ingredients = new ArrayCollection();

        $this->recordEvent(new CreatePizzaEvent($id, $name, $description, $price));
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

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}