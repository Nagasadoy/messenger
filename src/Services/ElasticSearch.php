<?php

namespace App\Services;

use App\Factory\UuidFactory;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\ClientErrorResponseException;
use Elasticsearch\Common\Exceptions\ServerErrorResponseException;

class ElasticSearch
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts(['elasticsearch:9200'])
            ->build();
    }

    public function getInfo()
    {
        $response = $this->client->info();
        return $response['version']['number'];
    }

    public function deleteIndex(string $indexName): array
    {
        $response = $this->client->indices()->delete(['index' => $indexName]);
        dd($response);

    }

    public function createIndex(string $indexName, array $mappings): array
    {
        $body = [
            'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'my_analyzer' => [
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'stop_words',
                                'stemmer'
                            ]
                        ]
                    ],
                    'filter' => [
                        'stop_words' => [
                            'type' => 'stop',
                            'stopwords' => '_russian_'
                        ],
                        'stemmer' => [
                            'type' => 'stemmer',
                            'language' => 'russian'
                        ],
                        'autocomplete_filter' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20
                        ]
                    ]
                ]
            ],
            'mappings' => $mappings
        ];

        // Mappings
//        [
//            'properties' => [
//                'name' => [
//                    'type' => 'text'
//                ],
//                'age' => [
//                    'type' => 'integer'
//                ],
//                'comment' => [
//                    'type' => 'text'
//                ]
//            ]
//        ]

        try {
            $response = $this->client->indices()->create([
                'index' => $indexName,
                'body' => $body
            ]);
        } catch (\Exception $e) {
            throw new \DomainException($e->getMessage());
        }
        return $response;
    }

    public function addDocument(string $indexName, array $body, ?string $id = null): void
    {
        if (null === $id) {
            $id = UuidFactory::generateNew();
        }

        $params = [
            'index' => $indexName,
            'body' => $body,
            'id' => $id
        ];

        try {
            $response = $this->client->index($params);
        } catch (ClientErrorResponseException|ServerErrorResponseException $e) {
            throw new \DomainException($e->getMessage());
        } catch (\Exception $e) {
            throw new \DomainException('Неизвестная ошибка!');
        }
    }

    public function search(string $index, array $query)
    {
        $params = [
            'index' => $index,
            'body' => [
                'query' => $query
            ]
        ];

        try {
            $response = $this->client->search($params);
        } catch (\Exception $exception) {
            throw new \DomainException('Что-то пошло не так!');
        }

        return $response['hits']['hits'] ?? [];
    }
}