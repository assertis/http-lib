<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Request\CachedRequest;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class CachedRequestTest extends PHPUnit_Framework_TestCase
{

    public function testGetters()
    {
        $cacheKey = 'cacheKey';
        $cacheFor = 100;
        $request = new CachedRequest('url', 'body', $cacheKey, $cacheFor);

        $this->assertEquals($cacheKey, $request->getCacheKey());
        $this->assertEquals($cacheFor, $request->getCacheFor());
    }
}
