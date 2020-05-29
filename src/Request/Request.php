<?php

declare(strict_types=1);

namespace Assertis\Http\Request;

use GuzzleHttp\Psr7\Uri;

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
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    const DEFAULT_TYPE = self::POST;

    /**
     * Url to send body to.
     *
     * @var string
     */
    private $url;

    /**
     * Url wrapped into PSR7 Uri object
     *
     * @var Uri
     */
    private $psr7url;

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
     * Headers of request
     *
     * @var array
     */
    private $headers;

    /**
     * Create request to send by http
     *
     * @param string $url
     * @param string $body
     * @param array $query
     * @param string $type
     * @param array $headers
     */
    public function __construct($url, $body = '', array $query = [], $type = self::DEFAULT_TYPE, $headers = [])
    {
        $this->url = $url;
        $this->psr7url = new Uri($this->url);
        $this->body = $body;
        $this->query = $query;
        $this->type = $type;
        $this->headers = $headers;
    }

    public static function get(string $url, array $query = [], array $headers = []): self
    {
        return new self($url, '', $query, self::GET, $headers);
    }

    public static function post(string $url, string $body = '', array $query = [], array $headers = []): self
    {
        return new self($url, $body, $query, self::POST, $headers);
    }

    public static function put(string $url, string $body = '', array $query = [], array $headers = []): self
    {
        return new self($url, $body, $query, self::PUT, $headers);
    }

    public static function delete(string $url, array $query = [], array $headers = []): self
    {
        return new self($url, '', $query, self::DELETE, $headers);
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
     * @return bool
     *  true - when $this->getUri() like http://host.com/resource
     *  false - when $this->getUri() like /resource/XYZ
     */
    public function hasFullUrl(): bool
    {
        return !empty($this->psr7url->getHost()) && !empty($this->psr7url->getScheme());
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
