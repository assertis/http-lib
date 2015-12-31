<?php

namespace Assertis\Http\Request;

/**
 * Interface for cached batch HTTP requests.
 *
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class CachedBatchRequest extends BatchRequest
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
     * @param string $cacheKey
     * @param int $cacheFor
     */
    public function __construct($cacheKey, $cacheFor)
    {
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
