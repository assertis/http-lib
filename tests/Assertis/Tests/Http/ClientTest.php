<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Client\Client;
use Assertis\Http\Request\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\RequestInterface;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

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
            'base_url' => 'http://test/',
        ]);
        $this->client = new Client($this->guzzleClient);
    }

    public function testShouldCreateRequest()
    {
        $request = new Request('/', self::BODY);
        $createdRequest = $this->client->createRequest($request);
        $this->assertEquals(self::BODY, $createdRequest->getBody());
        $this->assertEquals('', urldecode((string)$createdRequest->getQuery()));
        $this->assertEquals(80, $createdRequest->getPort());
        $this->assertEquals('http', $createdRequest->getScheme());
        $this->assertEquals('test', $createdRequest->getHost());
        $this->assertEquals('POST', $createdRequest->getMethod());
        $this->assertEquals('/', $createdRequest->getPath());
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
        $this->assertEquals('foo=bar&test[x]=y', urldecode((string)$createdRequest->getQuery()));
        $this->assertEquals(80, $createdRequest->getPort());
        $this->assertEquals('http', $createdRequest->getScheme());
        $this->assertEquals('test', $createdRequest->getHost());
        $this->assertEquals('POST', $createdRequest->getMethod());
        $this->assertEquals('/', $createdRequest->getPath());
    }

    public function testSendShouldCreateRequestAndSendIt()
    {
        /** @var RequestInterface|PHPUnit_Framework_MockObject_MockObject $httpRequest */
        $httpRequest = $this->getMockBuilder(RequestInterface::class)->getMock();

        /** @var GuzzleClient|PHPUnit_Framework_MockObject_MockObject $httpClient */
        $httpClient = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient->expects($this->once())
            ->method('createRequest')
            ->willReturn($httpRequest);

        $httpClient->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(RequestInterface::class));

        $request = new Request('/', self::BODY);
        $client = new Client($httpClient);
        $client->send($request);
    }
    
    public function testGetHttp()
    {
        $this->assertSame($this->guzzleClient, $this->client->getGuzzleClient());
    }
}
