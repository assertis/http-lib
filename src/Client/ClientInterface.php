<?php declare(strict_types = 1);

namespace Assertis\Http\Client;

use Assertis\Http\Request\BatchRequest;
use Assertis\Http\Request\Request;
use Assertis\Http\Response\BatchResults;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Http client interface
 *
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 */
interface ClientInterface
{
    /**
     * @param Request $request
     * @return RequestInterface
     */
    public function createRequest(Request $request): RequestInterface;

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

}
