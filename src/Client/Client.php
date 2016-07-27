<?php declare(strict_types = 1);

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
        $settings = [
            'body'  => $request->getBody(),
            'query' => $request->getQuery(),
        ];

        return $this->guzzleClient->createRequest($request->getType(), $request->getUrl(), $settings);
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
    public function sendBatch(BatchRequest $batchRequest): array
    {
        $requests = array_map([$this, 'createRequest'], $batchRequest->getRequests());
        $batchResults = Pool::batch($this->guzzleClient, $requests);

        if ($batchResults->getFailures()) {
            throw new BatchRequestFailureException($batchRequest, $batchResults);
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
