<?php

declare(strict_types=1);

namespace Assertis\Tests\Http;

use Assertis\Http\Request\Request;
use Generator;
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

    public function staticFactoryMethodsDataProvider(): Generator
    {
        $url = '/some/url';
        $body = '{}';
        $query = ['query' => 'parameter'];
        $headers = ['some' => 'header'];

        yield [
            'get',
            [$url, $query, $headers],
            Request::GET,
            $url,
            '',
            $query,
            $headers
        ];

        yield [
            'delete',
            [$url, $query, $headers],
            Request::DELETE,
            $url,
            '',
            $query,
            $headers
        ];

        yield [
            'post',
            [$url, $body, $query, $headers],
            Request::POST,
            $url,
            $body,
            $query,
            $headers
        ];

        yield [
            'put',
            [$url, $body, $query, $headers],
            Request::PUT,
            $url,
            $body,
            $query,
            $headers
        ];
    }

    /**
     * @dataProvider staticFactoryMethodsDataProvider
     */
    public function testStaticFactoryMethods(
        string $method,
        array $params,
        string $expectedType,
        string $expectedUrl,
        string $expectedBody,
        array $expectedQuery,
        array $expectedHeaders
    ) {
        $request = Request::$method(...$params);

        $this->assertEquals($expectedType, $request->getType());
        $this->assertEquals($expectedUrl, $request->getUrl());
        $this->assertEquals($expectedBody, $request->getBody());
        $this->assertEquals($expectedQuery, $request->getQuery());
        $this->assertEquals($expectedHeaders, $request->getHeaders());
    }
}
