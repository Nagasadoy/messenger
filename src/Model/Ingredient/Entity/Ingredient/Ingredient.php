<?php

namespace App\Model\Ingredient\Entity\Ingredient;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Ingredient
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;


    #[Orm\Column]
    private string $name;

    public function __construct(string $name, ?string $uid = null)
    {
        $this->name = $name;
        if (null === $uid) {
            $this->id = Uuid::v4();
        } else {
            $this->id = $uid;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }
}