<?php

namespace Assertis\Http\Client;

use Assertis\Http\Request\BatchRequest;
use Assertis\Http\Request\Request;
use Exception;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Pool;

/**
 * Http client for nrs service
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
     * Create request to send
     *
     * @param Request $request
     * @return RequestInterface
     */
    public function createRequest(Request $request)
    {
        $settings = [
            'body'  => $request->getBody(),
            'query' => $request->getQuery(),
        ];

        return $this->guzzleClient->createRequest($request->getType(), $request->getUrl(), $settings);
    }

    /**
     * Method send request for api
     *
     * @param \Assertis\Http\Request\Request $request
     * @return ResponseInterface
     */
    public function send(Request $request)
    {
        try {
            $response = $this->guzzleClient->send($this->createRequest($request));
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
        }

        return $response;
    }

    /**
     * Send multiple concurrent requests to the API.
     *
     * @param BatchRequest $batchRequest
     * @return ResponseInterface[]
     * @throws Exception
     */
    public function sendBatch(BatchRequest $batchRequest)
    {
        $requests = array_map([$this, 'createRequest'], $batchRequest->getRequests());
        $batchResults = Pool::batch($this->guzzleClient, $requests);

        if ($batchResults->getFailures()) {
            throw new Exception(
                "Encountered " . count($batchResults->getFailures()) . " failures while performing a batch request."
            );
        }

        return $batchResults->getSuccessful();
    }

    /**
     * @return GuzzleClientInterface
     */
    public function getGuzzleClient()
    {
        return $this->guzzleClient;
    }

    /**
     * @inheritdoc
     */
    public function attachSubscriber(SubscriberInterface $subscriber)
    {
        $this->guzzleClient->getEmitter()->attach($subscriber);
    }
}
