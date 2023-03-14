<?php

namespace App\Controller;

use App\Messenger\Message\GenerateRandomNumberMessage;
use Messenger\Message\RemoveRandIntMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    #[Route('/generate/int', name: 'index')]
    public function index(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new GenerateRandomNumberMessage());
        return $this->json(['message' => 'Rand int generated!']);
    }

    #[Route('/remove/int', methods: ['POST'])]
    public function removeNumberById(Request $request, MessageBusInterface $bus): Response
    {
        $content = $request->toArray();
        $id = $content['id'];

        $message = new \App\Messenger\Message\RemoveRandIntMessage((int)$id);

        $bus->dispatch($message);

        return $this->json([
            'message' => 'Number with id=' . $id . ' will be removed'
        ]);
    }
}