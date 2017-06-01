<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Client\ClientCached;
use Assertis\Http\Request\CachedBatchRequest;
use Assertis\Http\Request\CachedRequest;
use Assertis\Http\Request\Request;
use GuzzleHttp\Client as GuzzleClient;
use Memcached;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author loki
 */
class CachedClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Memcached|PHPUnit_Framework_MockObject_MockObject
     */
    private $memcacheMock;

    /**
     * @var GuzzleClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $guzzleClientMock;

    /**
     * @var ResponseInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $httpResponseMock;

    /**
     * @var CachedRequest|PHPUnit_Framework_MockObject_MockObject
     */
    private $cachedRequestMock;

    /**
     * @var CachedBatchRequest|PHPUnit_Framework_MockObject_MockObject
     */
    private $cachedBatchRequestMock;

    /**
     * @var Request|PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    const BODY = 'body';

    protected function setUp()
    {
        $this->memcacheMock = $this->createMock(Memcached::class);
        $this->guzzleClientMock = $this->createMock(GuzzleClient::class);
        $this->guzzleClientMock->method('getConfig')->with('base_url')->willReturn("http://test");
        $this->httpResponseMock = $this->createMock(ResponseInterface::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->cachedRequestMock = $this->createMock(CachedRequest::class);
        $this->cachedRequestMock->method('getQuery')->willReturn("");
        $this->cachedRequestMock->method('getUrl')->willReturn("");
        $this->cachedRequestMock->method('getHeaders')->willReturn([]);
        $this->cachedBatchRequestMock = $this->createMock(CachedBatchRequest::class);
    }

    public function testSendCachedWithoutCacheMakesRequest()
    {
        $cacheKey = 'CACHE_KEY';
        $body = 'BODY';

        $this->memcacheMock->method('get')->willReturn(false);
        $this->cachedRequestMock->expects($this->any())->method('getCacheKey')->willReturn($cacheKey);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);
        $this->httpResponseMock->expects($this->any())->method('getBody')->willReturn($stream);
        $this->guzzleClientMock->expects($this->once())->method('send')->willReturn($this->httpResponseMock);

        $client = new ClientCached($this->guzzleClientMock, $this->memcacheMock);
        $this->assertSame($body, $client->sendCached($this->cachedRequestMock));
    }

    public function testSendCachedWithCacheReturnsCache()
    {
        $cacheKey = 'CACHE_KEY';
        $body = 'BODY';

        $this->cachedRequestMock->expects($this->any())->method('getCacheKey')->willReturn($cacheKey);
        $this->guzzleClientMock->expects($this->never())->method('send');

        $this->memcacheMock->expects($this->once())->method('get')->with($cacheKey,null,null)->willReturn($body);
        $this->memcacheMock->expects($this->never())->method('set');

        $client = new ClientCached($this->guzzleClientMock, $this->memcacheMock);
        $this->assertSame($body, $client->sendCached($this->cachedRequestMock));
    }
}
