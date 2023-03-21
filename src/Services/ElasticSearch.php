<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

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
}