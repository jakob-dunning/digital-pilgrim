<?php
declare(strict_types = 1);
namespace Tests\ValueObject;

use App\ValueObject\UrlQueue;
use PHPUnit\Framework\TestCase;
use App\ValueObject\Url;

/**
 * @covers App\ValueObject\UrlQueue
 */
class UrlQueueTest extends TestCase
{

    public function testCreate()
    {
        $urlQueue = UrlQueue::create();
        
        $this->assertInstanceOf(UrlQueue::class, $urlQueue);
    }

    public function testContainsReturnsTrue()
    {
        $urlString = 'https://www.taz.de';
        $urlQueue = UrlQueue::create();
        $urlQueue->enqueue(Url::createFromString($urlString));
        
        $this->assertTrue($urlQueue->contains(Url::createFromString($urlString)));
    }

    public function testContainsReturnsFalse()
    {
        $urlString = 'https://www.taz.de';
        $urlQueue = UrlQueue::create();
        $urlQueue->enqueue(Url::createFromString($urlString));
        
        $this->assertFalse($urlQueue->contains(Url::createFromString('http://www.zeit.de')));
    }

    public function testJsonSerialize()
    {
        $urlString = 'https://www.taz.de';
        $urlQueue = UrlQueue::create();
        $urlQueue->enqueue(Url::createFromString($urlString));
        
        $this->assertSame('["https:\/\/www.taz.de"]', json_encode($urlQueue));
    }

    public function testGetIterator()
    {
        $urlArray = [
            Url::createFromString('https://www.taz.de'),
            Url::createFromString('https://www.zeit.de'),
            Url::createFromString('https://www.bild.de')
        ];
        $urlQueue = UrlQueue::create();
        foreach ($urlArray as $url) {
            $urlQueue->enqueue($url);
        }
        
        $this->assertInstanceOf(\ArrayIterator::class, $urlQueue->getIterator());
        $this->assertEquals(new \ArrayIterator($urlArray), $urlQueue->getIterator());
    }

    public function testEnQueue()
    {
        $firstUrlString = 'https://www.google.de';
        $urlQueue = UrlQueue::create();
        $urlQueue->enqueue(Url::createFromString($firstUrlString));
        
        $this->assertCount(1, $urlQueue->getIterator());
        
        $urlQueue->enqueue(Url::createFromString('https://www.hansa-pils.de'));
        $urlQueue->enqueue(Url::createFromString('https://www.radeberger.de'));
        
        $this->assertCount(3, $urlQueue->getIterator());
        
        $firstUrl = $urlQueue->dequeue();
        
        $this->assertEquals(Url::createFromString($firstUrlString), $firstUrl);
    }

    public function testDeQueue()
    {
        $firstUrlString = 'https://www.google.de';
        $urlQueue = UrlQueue::create();
        $urlQueue->enqueue(Url::createFromString($firstUrlString));
        $urlQueue->enqueue(Url::createFromString('https://www.hansa-pils.de'));
        $urlQueue->enqueue(Url::createFromString('https://www.radeberger.de'));
        
        $this->assertCount(3, $urlQueue->getIterator());
        
        $firstUrl = $urlQueue->dequeue();
        
        $this->assertEquals(Url::createFromString($firstUrlString), $firstUrl);
        $this->assertCount(2, $urlQueue->getIterator());
    }

    public function testIsEmptyReturnsTrue()
    {
        $urlQueue = UrlQueue::create();
        
        $this->assertTrue($urlQueue->isEmpty());
    }

    public function testIsEmptyReturnsFalse()
    {
        $urlQueue = UrlQueue::createFromArray([
            'https://www.aldis-rache.de/bonsai/'
        ]);
        
        $this->assertFalse($urlQueue->isEmpty());
    }
}

