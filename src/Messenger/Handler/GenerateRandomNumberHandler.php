<?php

namespace App\Messenger\Handler;

use App\Entity\RandNumber;
use App\Messenger\Message\GenerateRandomNumberMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenerateRandomNumberHandler
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(GenerateRandomNumberMessage $message)
    {
        $randNumber = new RandNumber();
        $value = $message->getRandomInt();

        $randNumber->setValue($value);

        $this->entityManager->persist($randNumber);
        $this->entityManager->flush();
    }
}