<?php

namespace App\Controller;

use App\Services\ElasticSearch;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(private readonly PaginatedFinderInterface $finder)
    {
    }

    #[Route('api/users', methods: ['GET'])]
    public function find(Request $request, ElasticSearch $elasticSearch): Response
    {
        $query = $request->toArray()['query'] ?? '';
        $elasticSearch->search('people_index', [
            'match_phrase_prefix' => [
                'comment' => $query
            ]
        ]);

//        $elasticSearch->search('people_index',[
//            'bool' => [
//                'should' => [
//                    'term' => [
//                        'comment' => 'план'
//                    ]
//                ]
//            ]
//        ]);

//        $elasticSearch->addDocument('people_index', [
//            'name' => 'alexey',
//            'age' => 24,
//            'comment' => 'Москва слезам не верит'
//        ]);

//        $elasticSearch->createIndex('people_index');
//        $elasticSearch->deleteIndex('people_index');


        return $this->json([
            'message' => 'message'
        ]);
    }
}