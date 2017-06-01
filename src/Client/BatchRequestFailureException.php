<?php declare(strict_types = 1);

namespace Assertis\Http\Client;

use Assertis\Http\Request\BatchRequest;
use Exception;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class BatchRequestFailureException extends Exception
{
    /**
     * @var BatchRequest
     */
    private $batchRequest;
    /**
     * @var array
     */
    private $batchResults;

    /**
     * @param BatchRequest $batchRequest
     * @param array $batchResults
     */
    public function __construct(BatchRequest $batchRequest, array $batchResults)
    {
        $message = 'Encountered failures while performing a batch request.';
        
        parent::__construct($message);
        
        $this->batchRequest = $batchRequest;
        $this->batchResults = $batchResults;
    }

    /**
     * @return BatchRequest
     */
    public function getBatchRequest(): BatchRequest
    {
        return $this->batchRequest;
    }

    /**
     * @return array
     */
    public function getBatchResults(): array
    {
        return $this->batchResults;
    }
}
