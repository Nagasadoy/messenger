<?php

namespace App\Controller;

use App\Messenger\Message\GenerateRandomNumberMessage;
use App\Messenger\Message\LogEmoji;
use Messenger\Message\RemoveRandIntMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    #[Route('/generate/int', name: 'index')]
    public function index(MessageBusInterface $bus): Response
    {
        $message = new GenerateRandomNumberMessage();
        $envelope = new Envelope($message, [
            new DelayStamp(5000),
//            new AmqpStamp('messages_normal')
        ]);
        $bus->dispatch(new LogEmoji(2));
        $bus->dispatch($envelope);
        return $this->json(['message' => 'Rand int generated!']);
    }

    #[Route('/remove/int', methods: ['POST'])]
    public function removeNumberById(Request $request, MessageBusInterface $bus): Response
    {
        $content = $request->toArray();
        $id = $content['id'];

        $message = new \App\Messenger\Message\RemoveRandIntMessage((int)$id);

        $envelope = new Envelope($message, [
            new DelayStamp(10000)
        ]);

        $bus->dispatch($envelope);

        return $this->json([
            'message' => 'Number with id=' . $id . ' will be removed'
        ]);
    }
}