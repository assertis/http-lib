<?php

namespace Assertis\Http\Client;

use Assertis\Http\Request\Request;

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
     * @param \Assertis\Http\Request\Request $request
     * @return mixed
     */
    public function createRequest(Request $request);

    /**
     * Method send request for api
     *
     * @param \Assertis\Http\Request\Request $request
     * @return mixed
     */
    public function send(Request $request);
}
