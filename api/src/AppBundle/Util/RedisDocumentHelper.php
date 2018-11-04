<?php

namespace AppBundle\Util;

use Ehann\RedisRaw\PhpRedisAdapter;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\Exceptions\UnknownIndexNameException;

class RedisDocumentHelper
{
    const MIN_TTL = 1;
    const MAX_TTL = 3600;

    private $host;
    private $port;
    private $indexName;
    private $index;

    public function __construct()
    {
        $this->host = 'localhost';
        $this->port = '6379';
        $this->indexName = 'default_index_0';
        $this->index = $this->connect();
    }

    /**
     * fetches a document
     * @param $userIdentifier
     * @return \Ehann\RediSearch\Query\SearchResult
     */
    public function getDocument($userIdentifier)
    {
        return $this->index
            ->inFields(1, ['userIdentifier'])
            ->search($userIdentifier, true);
    }

    /**
     * adds a new document
     * @param $userIdentifier
     * @param $contents
     * @return bool
     * @throws \Ehann\RediSearch\Exceptions\FieldNotInSchemaException
     */
    public function addDocument($userIdentifier, $contents)
    {
        $this->index->add([
            'contents' => $contents,
            'userIdentifier'=> $userIdentifier
        ]);

        return $this->getDocument($userIdentifier);
    }

    /**
     * searches content fields for words
     * @param $query
     * @return \Ehann\RediSearch\Query\SearchResult
     */
    public function search($query){
        return $this->index->limit(0, 100)
            ->inFields(1, ['contents'])
            ->search($query, true);
    }

    /**
     * deletes a document by a custom identifier
     * @param $identifier
     * @return \Ehann\RediSearch\Query\SearchResult
     */
    public function deleteDocument($identifier)
    {
        $document = $this->index
            ->inFields(1, ['userIdentifier'])
            ->search($identifier, true);

        if ($document->getCount() > 0) {
            $redisId = $document->getDocuments()[0]['id'];
            $this->index->delete($redisId);
        }
        return $document;
    }

    /**
     * connect and make sure that the index is created and has the required fields
     * @return Index
     * @throws \Ehann\RediSearch\Exceptions\NoFieldsInIndexException
     */
    private function connect()
    {
        $redis = (new PhpRedisAdapter())->connect($this->host, $this->port);

        $documentIndex = new Index($redis, $this->indexName);

        // create the index if it does not exist yet, first request
        $documentIndex->addTextField('userIdentifier');
        $documentIndex->addTextField('contents');

        try {
            $documentIndex->info();
        } catch (UnknownIndexNameException $e){
            $documentIndex->create();
        }

        return $documentIndex;
    }
}