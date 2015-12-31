<?php

namespace Assertis\Http\Request;

/**
 * Interface for batch HTTP requests.
 *
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class BatchRequest
{
    /**
     * Requests to send in batch.
     *
     * @var Request[]
     */
    private $requests = [];

    /**
     * Add request to batch.
     *
     * @param Request $request
     */
    public function addRequest(Request $request)
    {
        $this->requests[] = $request;
    }

    /**
     * @param Request[] $requests
     */
    public function addRequests($requests)
    {
        foreach ($requests as $request) {
            $this->addRequest($request);
        }
    }

    /**
     * Get the list of requests.
     *
     * @return Request[]
     */
    public function getRequests()
    {
        return $this->requests;
    }
}
