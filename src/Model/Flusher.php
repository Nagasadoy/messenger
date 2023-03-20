<?php

namespace App\Model;

use Doctrine\ORM\EntityManagerInterface;

class Flusher
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcher $dispatcher
    ) { }

    public function flush(AggregateRoot ...$roots): void
    {
        $this->em->flush();

        foreach ($roots as $root) {
            $this->dispatcher->dispatch($root->releaseEvents());
        }
    }
}