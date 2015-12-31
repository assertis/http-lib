<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Request\CachedBatchRequest;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class CachedBatchRequestTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $cacheKey = 'cacheKey';
        $cacheFor = 100;
        $request = new CachedBatchRequest($cacheKey, $cacheFor);

        $this->assertEquals($cacheKey, $request->getCacheKey());
        $this->assertEquals($cacheFor, $request->getCacheFor());
    }
}
