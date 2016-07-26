<?php declare(strict_types = 1);

namespace Assertis\Http\Client;

use Assertis\Http\Request\BatchRequest;
use Assertis\Http\Request\Request;
use GuzzleHttp\BatchResults;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Http client interface
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
interface ClientInterface
{
    /**
     * Send a single request.
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function send(Request $request): ResponseInterface;

    /**
     * Send multiple requests in parallel.
     *
     * @param BatchRequest $requests
     * @return BatchResults
     */
    public function sendBatch(BatchRequest $requests): BatchResults;

    /**
     * Attach a subscriber that gets notified of requests.
     *
     * @param SubscriberInterface $subscriber
     */
    public function attachSubscriber(SubscriberInterface $subscriber);
}
