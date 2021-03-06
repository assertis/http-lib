<?php

namespace Assertis\Http\Client;

use GuzzleHttp\Client as GuzzleClient;
use InvalidArgumentException;
use Memcache;

/**
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class ClientFactory
{
    const DEFAULT_CONNECT_TIMEOUT = 2;
    const DEFAULT_REQUEST_TIMEOUT = 10;

    /**
     * @var Memcache|null
     */
    private $memcache;

    /**
     * @param Memcache $memcache
     */
    public function __construct(Memcache $memcache = null)
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
     * @param string|null $tenant
     * @param callable $handler
     * @return ClientInterface
     */
    public function getClient(
        $baseUrl,
        $contentType,
        $isCached,
        $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT,
        $requestTimeout = self::DEFAULT_REQUEST_TIMEOUT,
        array $auth = null,
        $tenant = null,
        callable $handler = null
    ) {
        $params = [
            'base_uri' => $baseUrl,
            'handler' => $handler,
            'defaults' => [
                'headers'         => [
                    'content-type' => $contentType,
                ],
                'timeout'         => $requestTimeout,
                'connect_timeout' => $connectTimeout,
            ],
        ];

        if ($auth) {
            $params['defaults']['auth'] = $params['auth'] = $auth;
        }
        
        if ($tenant) {
            $params['defaults']['headers']['X-TENANT'] = $tenant;
        }

        $client = new GuzzleClient($params);

        if ($isCached && empty($this->memcache)) {
            throw new InvalidArgumentException('Memcache client required but not provided.');
        }

        return $isCached ?
            new ClientCached($client, $this->memcache) :
            new Client($client);
    }
}
