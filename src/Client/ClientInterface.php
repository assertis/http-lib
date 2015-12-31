<?php

namespace Assertis\Http\Client;

use Assertis\Http\Request\Request;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Http client interface
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
interface ClientInterface
{
    /**
     * Create request to send
     *
     * @param Request $request
     * @return RequestInterface
     */
    public function createRequest(Request $request);

    /**
     * Method send request for api
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function send(Request $request);

    /**
     * @param SubscriberInterface $subscriber
     */
    public function attachSubscriber(SubscriberInterface $subscriber);
}
