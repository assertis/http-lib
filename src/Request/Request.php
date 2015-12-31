<?php

namespace Assertis\Http\Request;

/**
 * Interface for http requests
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class Request
{
    /**
     * Request type
     */
    const POST = 'POST';
    const GET = 'GET';

    const DEFAULT_TYPE = self::POST;

    /**
     * Url to send body to.
     *
     * @var string
     */
    private $url;

    /**
     * Body of request to send
     *
     * @var string
     */
    private $body;

    /**
     * Query of request
     *
     * @var array
     */
    private $query;

    /**
     * Type of request
     *
     * @var string
     */
    private $type;

    /**
     * Create request to send by http
     *
     * @param string $url
     * @param string $body
     * @param array $query
     * @param string $type
     */
    public function __construct($url, $body = '', array $query = [], $type = self::DEFAULT_TYPE)
    {
        $this->url = $url;
        $this->body = $body;
        $this->query = $query;
        $this->type = $type;
    }

    /**
     * Type of request
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return body of request
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set request of http body
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
}
