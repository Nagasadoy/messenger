<?php

namespace App\Controller;

use App\Services\ElasticSearch;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly PaginatedFinderInterface $finder)
    {
    }

    #[Route('api/users', methods: ['GET'])]
    public function find(ElasticSearch $elasticSearch): Response
    {
//        $results = $this->finder->find('Alex');
//        dd($results);

        $info = $elasticSearch->getInfo();
        return $this->json([
            'info' => $info
        ]);
    }
}