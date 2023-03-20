<?php

namespace App\Event\Listener\Pizza;

use App\Model\Pizza\Entity\Pizza\Event\AddIngredientEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PizzaAddIngredientSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AddIngredientEvent::class => 'onAddIngredient'
        ];
    }

    public function onAddIngredient(AddIngredientEvent $event): void
    {
        $this->logger->info('Pizza add ingredient with id ' . $event->ingredientId);
    }

}