<?php

namespace Assertis\Http\Client;

use Assertis\Http\Request\CachedBatchRequest;
use Assertis\Http\Request\CachedRequest;
use GuzzleHttp\Client as GuzzleClient;
use Memcached;
use Psr\Http\Message\ResponseInterface;

/**
 * Caching implementation for http client for nrs service.
 *
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class ClientCached extends Client
{
    /**
     * @var Memcached
     */
    private $memcache;

    /**
     * @param GuzzleClient $http
     * @param Memcached $memcache
     */
    public function __construct(GuzzleClient $http, Memcached $memcache)
    {
        parent::__construct($http);
        $this->memcache = $memcache;
    }

    /**
     * Method send request for api
     *
     * @param CachedRequest $request
     * @return string
     */
    public function sendCached(CachedRequest $request)
    {
        $k = $request->getCacheKey();
        $cached = $this->memcache->get($k);
        if (false !== $cached) {
            return $cached;
        }

        $response = $this->send($request);
        $body = $response->getBody()->getContents();
        $this->memcache->set($request->getCacheKey(), $body, 0);

        return $body;
    }

    /**
     * Send multiple concurrent requests to the API.
     *
     * @param CachedBatchRequest $batchRequest
     * @return string[]
     */
    public function sendCachedBatch(CachedBatchRequest $batchRequest)
    {
        $cached = $this->memcache->get($batchRequest->getCacheKey());
        if (false !== $cached) {
            return unserialize($cached);
        }

        $responses = $this->sendBatch($batchRequest);
        $out = [];

        /* @var ResponseInterface $response */
        foreach ($responses as $response) {
            $out[] = is_object($response) ? (string)$response->getBody()->getContents() : null;
        }

        $this->memcache->set($batchRequest->getCacheKey(), serialize($out));

        return $out;
    }
}
