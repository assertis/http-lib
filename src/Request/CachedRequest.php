<?php

namespace Assertis\Http\Request;

/**
 * Caching extension for HTTP Request
 *
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class CachedRequest extends Request
{
    /**
     * Number of seconds to cache the result for.
     *
     * @var int
     */
    private $cacheFor;

    /**
     * Key under which to cache the return value.
     *
     * @var int
     */
    private $cacheKey;

    /**
     * Create request to send by http
     *
     * @param string $url
     * @param string $body
     * @param string $cacheKey
     * @param int $cacheFor
     * @param string $type
     */
    public function __construct($url, $body, $cacheKey, $cacheFor, $type = self::DEFAULT_TYPE)
    {
        parent::__construct($url, $body, [], $type);
        $this->cacheKey = $cacheKey;
        $this->cacheFor = $cacheFor;
    }

    /**
     * Number of seconds to cache the result for.
     *
     * @return int
     */
    public function getCacheFor()
    {
        return $this->cacheFor;
    }

    /**
     * Key under which to cache the return value.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }
}
