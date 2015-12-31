<?php

namespace Assertis\Http\Client;

use GuzzleHttp\Client;
use Memcache;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ClientFactory
{
    const DEFAULT_CONNECT_TIMEOUT = 2;
    const DEFAULT_REQUEST_TIMEOUT = 10;

    /**
     * @var Memcache
     */
    private $memcache;

    /**
     * @param Memcache $memcache
     */
    public function __construct(Memcache $memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * @param string $baseUrl
     * @param string $contentType
     * @param bool $isCached
     * @param int $connectTimeout
     * @param int $requestTimeout
     * @param array $auth
     * @return ClientInterface
     */
    public function getClient(
        $baseUrl,
        $contentType,
        $isCached,
        $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT,
        $requestTimeout = self::DEFAULT_REQUEST_TIMEOUT,
        array $auth = null
    ) {
        $params = [
            'base_url' => $baseUrl,
            'defaults' => [
                'headers'         => [
                    'content-type' => $contentType,
                ],
                'timeout'         => $requestTimeout,
                'connect_timeout' => $connectTimeout,
            ],
        ];

        if ($auth) {
            $params['defaults']['auth'] = $auth;
        }

        $client = new Client($params);

        return $isCached ?
            new ClientCached($client, $this->memcache) :
            new Client($client);
    }
}
