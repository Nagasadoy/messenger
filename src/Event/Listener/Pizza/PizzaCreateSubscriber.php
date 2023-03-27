<?php

namespace App\Event\Listener\Pizza;

use App\Model\Pizza\Entity\Pizza\Event\CreatePizzaEvent;
use App\Model\Pizza\Entity\Pizza\Pizza;
use App\Services\ElasticSearch\ElasticSearch;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PizzaCreateSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ElasticSearch $elasticSearch, private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreatePizzaEvent::class => 'onCreate'
        ];
    }

    public function onCreate(CreatePizzaEvent $event): void
    {
        $this->logger->info('Pizza created with name ' . $event->name);
        $this->elasticSearch->addDocument(Pizza::PIZZA_INDEX, [
            'name' => $event->name,
            'price' => $event->price,
            'description' => $event->description,
            'id' => $event->pizzaId
        ], $event->pizzaId);

    }
}