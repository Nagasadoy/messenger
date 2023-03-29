<?php

namespace App\Controller;

use App\Services\RabbitMqNative\RabbitMq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RabbitmqController extends AbstractController
{
    #[Route('api/rabbitmq_send')]
    public function sendHello(RabbitMq $rabbitMq): Response
    {

        $message = 'HELLO FROM SOFTINVENT';

        $rabbitMq->sendMessage($message);

        return $this->json([
            'message' => 'message "' . $message .'" received'
        ]);
    }

    #[Route('api/rabbitmq_consume')]
    public function consumeHello(RabbitMq $rabbitMq): Response
    {
//        $rabbitMq->consume();
//        $rabbitMq->consumeDirect();
//        $rabbitMq->consumeTopic();
        $rabbitMq->consumeFanout();

        return $this->json([
            'message' => 'message received maybe'
        ]);
    }

    #[Route('api/direct_exchanges')]
    public function logs(RabbitMq $rabbitMq): Response
    {
        $rabbitMq->sendInfo();
        return $this->json([
            'message' => 'message received maybe'
        ]);
    }

    #[Route('api/topic_exchanges')]
    public function topicExchange(RabbitMq $rabbitMq): Response
    {
        $rabbitMq->sendTopic();
        return $this->json([
            'message' => 'message received maybe'
        ]);
    }

    #[Route('api/fanout_exchanges')]
    public function fanoutExchange(RabbitMq $rabbitMq): Response
    {
        $rabbitMq->sendFanout();
        return $this->json([
            'message' => 'message received maybe'
        ]);
    }
}