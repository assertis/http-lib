<?php

namespace Assertis\Http\Client;

use Assertis\Http\Request\CachedBatchRequest;
use Assertis\Http\Request\CachedRequest;
use GuzzleHttp\Client as GuzzleClient;
use Memcache;

/**
 * Caching implementation for http client for nrs service.
 *
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class ClientCached extends Client
{
    /**
     * @var Memcache
     */
    private $memcache;

    /**
     * @param GuzzleClient $http
     * @param Memcache $memcache
     */
    public function __construct(GuzzleClient $http, Memcache $memcache)
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
        $cached = $this->memcache->get($request->getCacheKey());
        if (false !== $cached) {
            return $cached;
        }

        $response = $this->send($request);
        $this->memcache->set($request->getCacheKey(), (string)$response->getBody(), 0, $request->getCacheFor());

        return $response->getBody();
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

        foreach ($responses as $response) {
            $out[] = is_object($response) ? (string)$response->getBody() : null;
        }

        $this->memcache->set($batchRequest->getCacheKey(), serialize($out), 0, $batchRequest->getCacheFor());

        return $out;
    }
}
