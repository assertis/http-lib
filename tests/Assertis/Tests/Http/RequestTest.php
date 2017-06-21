<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Request\Request;
use PHPUnit_Framework_TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $url = 'url';
        $body = 'body';
        $headers = ['X-Auth' => '123'];
        $request = new Request($url, $body, [], Request::DEFAULT_TYPE, $headers);

        $this->assertEquals($url, $request->getUrl());
        $this->assertEquals($body, $request->getBody());
        $this->assertEquals(Request::DEFAULT_TYPE, $request->getType());
        $this->assertEquals($headers, $request->getHeaders());
    }

    public function testSetBody()
    {
        $url = 'url';
        $body = 'body';
        $newBody = 'newBody';

        $request = new Request($url, $body);
        $this->assertEquals($body, $request->getBody());
        $request->setBody($newBody);
        $this->assertEquals($newBody, $request->getBody());
    }

    public function hasFullUrlDataProvider()
    {
        return [
            ['/resource/XYZ', false],
            ['/', false],
            ['/sampleendpoint', false],
            ['host.com/endpoint', false],
            ['http://host.com', true],
            ['http://host.com/', true],
            ['http://host.com/endpoint', true],
            ['http://host.com/resource/XYZ', true],
        ];
    }

    /**
     * @dataProvider hasFullUrlDataProvider
     * @param $url
     * @param $expected
     */
    public function testHasFullUrl($url, $expected)
    {
        $request = new Request($url, '', []);
        $this->assertEquals($expected, $request->hasFullUrl());
    }
}
