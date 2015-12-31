<?php

namespace Assertis\Test\Http;

use Assertis\Http\Request\BatchRequest;
use Assertis\Http\Request\Request;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class BatchRequestTest extends PHPUnit_Framework_TestCase
{

    public function testGetters()
    {
        $url = 'url';
        $body = 'body';
        $request = new Request($url, $body);
        $batchRequest = new BatchRequest();
        $batchRequest->addRequest($request);
        $this->assertSame([$request], $batchRequest->getRequests());
    }

    public function testAddRequests()
    {
        $requests = [
            new Request('url1', 'body1'),
            new Request('url2', 'body2'),
        ];

        $batchRequest = new BatchRequest();
        $batchRequest->addRequests($requests);
        $this->assertSame($requests, $batchRequest->getRequests());
    }
}
