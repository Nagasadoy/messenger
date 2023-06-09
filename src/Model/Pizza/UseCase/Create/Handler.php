<?php

namespace App\Model\Pizza\UseCase\Create;

use App\Factory\UuidFactory;
use App\Model\Flusher;
use App\Model\Ingredient\UseCase\Create\CreateIngredientCommand;
use App\Model\Pizza\Entity\Pizza\Pizza;
use App\Model\Pizza\Entity\Pizza\PizzaRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

class Handler
{
    public function __construct(
        private readonly PizzaRepository $pizzaRepository,
        private readonly Flusher $flusher,
        private readonly MailerInterface $mailer
    ) { }

    public function handle(CreatePizzaCommand $command): void
    {
        $id = UuidFactory::generateNew();
        $name = $command->name;
        $price = $command->price;
        $description = $command->description;

        $pizza = new Pizza($id, $name, $price, $description);
        $this->pizzaRepository->add($pizza);
        $this->flusher->flush($pizza);
    }
}