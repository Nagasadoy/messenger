<?php

namespace App\Controller;

use Messenger\Message\RemoveRandIntMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    #[Route('/hello', name: 'index')]
    public function index(): Response
    {
        return $this->json(['message' => 'hello']);
    }
}