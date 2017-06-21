<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Client\Client;
use Assertis\Http\Request\Request;
use GuzzleHttp\Client as GuzzleClient;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author loki
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    const BODY = 'body';

    protected function setUp()
    {
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => 'http://test/',
        ]);
        $this->client = new Client($this->guzzleClient);
    }

    public function testShouldCreateRequest()
    {
        $request = new Request('endpoint', self::BODY);
        $createdRequest = $this->client->createRequest($request);
        $this->assertEquals(self::BODY, $createdRequest->getBody()->getContents());
        $this->assertEquals('', urldecode((string)$createdRequest->getUri()->getQuery()));
        $this->assertEquals('http', $createdRequest->getUri()->getScheme());
        $this->assertEquals('test', $createdRequest->getUri()->getHost());
        $this->assertEquals('POST', $createdRequest->getMethod());
        $this->assertEquals('/endpoint', $createdRequest->getUri()->getPath());
    }

    public function testShouldCreateRequestWithFullUrlInRequest()
    {
        $request = new Request('http://sample.com/endpoint', '', [], Request::GET);
        $createdRequest = $this->client->createRequest($request);
        $this->assertEquals('', $createdRequest->getBody()->getContents());
        $this->assertEquals('', urldecode((string)$createdRequest->getUri()->getQuery()));
        $this->assertEquals('http', $createdRequest->getUri()->getScheme());
        $this->assertEquals('sample.com', $createdRequest->getUri()->getHost());
        $this->assertEquals('GET', $createdRequest->getMethod());
        $this->assertEquals('/endpoint', $createdRequest->getUri()->getPath());
    }

    public function testShouldCreateRequestWithQuery()
    {
        $query = [
            'foo' => 'bar',
            'test' => [
                'x' => 'y'
            ]
        ];
        $request = new Request("/", '', $query);
        $createdRequest = $this->client->createRequest($request);
        $this->assertEquals('', $createdRequest->getBody());
        $this->assertEquals('foo=bar&test[x]=y', urldecode((string)$createdRequest->getUri()->getQuery()));
        $this->assertEquals('http', $createdRequest->getUri()->getScheme());
        $this->assertEquals('test', $createdRequest->getUri()->getHost());
        $this->assertEquals('POST', $createdRequest->getMethod());
        $this->assertEquals('/', $createdRequest->getUri()->getPath());
    }

    public function testSendShouldCreateRequestAndSendIt()
    {
        /** @var GuzzleClient|PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willReturn($this->getMockForAbstractClass(ResponseInterface::class));

        $httpClient
            ->method('getConfig')
            ->with('base_uri')
            ->willReturn("http://test");

        $request = new Request('/', self::BODY);
        $client = new Client($httpClient);
        $client->send($request);
    }
    
    public function testGetHttp()
    {
        $this->assertSame($this->guzzleClient, $this->client->getGuzzleClient());
    }
}
