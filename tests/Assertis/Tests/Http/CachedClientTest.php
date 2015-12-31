<?php

namespace Assertis\Tests\Http;

use Assertis\Http\Client\ClientCached;
use Assertis\Http\Request\CachedBatchRequest;
use Assertis\Http\Request\CachedRequest;
use Assertis\Http\Request\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Collection;
use GuzzleHttp\Event\EmitterInterface;
use GuzzleHttp\Message\FutureResponse;
use GuzzleHttp\Message\RequestInterface;
use Memcache;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * @author loki
 */
class CachedClientTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Memcache|PHPUnit_Framework_MockObject_MockObject
     */
    private $memcacheMock;

    /**
     * @var GuzzleClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $guzzleClientMock;

    /**
     * @var RequestInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $guzzleRequestMock;

    /**
     * @var FutureResponse|PHPUnit_Framework_MockObject_MockObject
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
        $this->memcacheMock = $this->getMock(Memcache::class, ['get', 'set']);
        $this->guzzleClientMock = $this->getMockBuilder(GuzzleClient::class)
            ->setMethods(['createRequest', 'send'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->httpResponseMock = $this->getMockBuilder(FutureResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cachedRequestMock = $this->getMockBuilder(CachedRequest::class)
            ->setMethods(['getCacheKey', 'getCacheFor'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->cachedBatchRequestMock = $this->getMockBuilder(CachedBatchRequest::class)
            ->setMethods(['getCacheKey', 'getCacheFor'])
            ->disableOriginalConstructor()
            ->getMock();

        $config = new Collection();
        $emitter = $this->getMock(EmitterInterface::class);
        $this->guzzleRequestMock = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->guzzleRequestMock->expects($this->any())->method('getConfig')->willReturn($config);
        $this->guzzleRequestMock->expects($this->any())->method('getEmitter')->willReturn($emitter);
    }

    public function testSendCachedWithoutCacheMakesRequest()
    {
        $cacheKey = 'CACHE_KEY';
        $cacheFor = 100;
        $body = 'BODY';

        $this->cachedRequestMock->expects($this->any())->method('getCacheKey')->willReturn($cacheKey);
        $this->cachedRequestMock->expects($this->once())->method('getCacheFor')->willReturn($cacheFor);

        $this->httpResponseMock->expects($this->any())->method('getBody')->willReturn($body);

        $this->guzzleClientMock->expects($this->once())->method('createRequest')->willReturn($this->guzzleRequestMock);
        $this->guzzleClientMock->expects($this->once())->method('send')->willReturn($this->httpResponseMock);

        $this->memcacheMock->expects($this->once())->method('get')->with($cacheKey)->willReturn(false);
        $this->memcacheMock->expects($this->once())->method('set')->with($cacheKey, $body, 0, $cacheFor);

        $client = new ClientCached($this->guzzleClientMock, $this->memcacheMock);
        $this->assertSame($body, $client->sendCached($this->cachedRequestMock));
    }

    public function testSendCachedWithCacheReturnsCache()
    {
        $cacheKey = 'CACHE_KEY';
        $body = 'BODY';

        $this->cachedRequestMock->expects($this->any())->method('getCacheKey')->willReturn($cacheKey);
        $this->cachedRequestMock->expects($this->never())->method('getCacheFor');

        $this->guzzleClientMock->expects($this->never())->method('send');

        $this->memcacheMock->expects($this->once())->method('get')->with($cacheKey)->willReturn($body);
        $this->memcacheMock->expects($this->never())->method('set');

        $client = new ClientCached($this->guzzleClientMock, $this->memcacheMock);
        $this->assertSame($body, $client->sendCached($this->cachedRequestMock));
    }
}
