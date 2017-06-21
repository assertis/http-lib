<?php declare(strict_types=1);

namespace Assertis\Http\Client;

use Assertis\Http\Request\BatchRequest;
use Assertis\Http\Request\Request;
use Assertis\Http\Response\BatchResults;
use Exception;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * A simplified HTTP client.
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
class Client implements ClientInterface
{
    /**
     * Http client
     *
     * @var GuzzleClientInterface
     */
    protected $guzzleClient;

    /**
     * @param GuzzleClientInterface $http
     */
    public function __construct(GuzzleClientInterface $http)
    {
        $this->guzzleClient = $http;
    }

    /**
     * @inheritdoc
     */
    public function createRequest(Request $request): RequestInterface
    {
        $body = $request->getBody();
        $headers = $request->getHeaders();
        $query = empty($request->getQuery()) ? "" : "?".http_build_query($request->getQuery());
        if($request->hasFullUrl()){
            $uri = new Uri($request->getUrl());
        } else {
            $rawBaseUrl = (string)$this->guzzleClient->getConfig("base_uri");
            if(empty($rawBaseUrl)){
                throw new RuntimeException("Base url is not provided!");
            }
            // trimming is here to avoid issues with "/" - too much slashes or missing slashes.
            $baseUrl = rtrim($rawBaseUrl, "/");
            $url = "/".ltrim($request->getUrl(),'/');
            $uri = new Uri($baseUrl . $url . $query);
        }

        return new GuzzleRequest($request->getType(), $uri, $headers, $body);
    }

    /**
     * @inheritdoc
     */
    public function send(Request $request): ResponseInterface
    {
        try {
            $response = $this->guzzleClient->send($this->createRequest($request));
        } catch (Exception $exception) {
            if ($exception instanceof RequestException) {
                $response = $exception->getResponse();

                if (empty($response)) {
                    throw $exception;
                }

            } else {
                throw $exception;
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function sendBatch(BatchRequest $batchRequest): BatchResults
    {
        $requests = array_map([$this, 'createRequest'], $batchRequest->getRequests());
        $batchResults = new BatchResults(Pool::batch($this->guzzleClient, $requests));

        if ($batchResults->getFailures()) {
            throw new BatchRequestFailureException($batchRequest, $batchResults);
        }

        return $batchResults;
    }

    /**
     * @return GuzzleClientInterface
     */
    public function getGuzzleClient(): GuzzleClientInterface
    {
        return $this->guzzleClient;
    }

}
