<?php

namespace App\Messenger\Handler;

use App\Repository\RandNumberRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Messenger\Message\RemoveRandIntMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveRandIntMessageHandler
{

    public function __construct(private readonly EntityManagerInterface $em, private RandNumberRepository $numberRepository)
    {
    }

    public function __invoke(RemoveRandIntMessage $message): void
    {
        $id = $message->getId();

        $removedNumber = $this->numberRepository->find($id);

        if ($removedNumber === null) {
            throw new \DomainException('Нет такого id');
        }

        $this->em->remove($removedNumber);
        $this->em->flush();
    }
}