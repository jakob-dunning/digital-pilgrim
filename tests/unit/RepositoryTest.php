<?php
declare(strict_types = 1);
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Repository;
use App\Service\Storage\StorageService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;
use App\ValueObject\StorageItem\FileStorageItem;

/**
 * @covers App\Repository
 */
class RepositoryTest extends TestCase
{

    private $subject;

    private $storageServiceMock;

    public function setUp()
    {
        $this->storageServiceMock = $this->createMock(StorageService::class);
        $this->subject = new Repository($this->storageServiceMock);
    }

    public function testGetScraperQueue()
    {
        $key = 'scraperQueue';
        $urlStringArray = [
            'http://www.ccc.de',
            'http://www.bier.de'
        ];
        $this->storageServiceMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(FileStorageItem::create($key, json_encode($urlStringArray)));
        
        $scraperQueue = $this->subject->getScraperQueue();
        
        $this->assertEquals(UrlQueue::createFromArray($urlStringArray), $scraperQueue);
    }

    public function testGetCurrentDomain()
    {
        $key = 'currentDomain';
        $urlString = 'http://www.ccc.de';
        $this->storageServiceMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(FileStorageItem::create($key, json_encode($urlString)));
        
        $scraperQueue = $this->subject->getCurrentDomain();
        
        $this->assertEquals(Url::createFromString($urlString), $scraperQueue);
    }

    public function testGetDestinations()
    {
        $key = 'destinations';
        $urlStringArray = [
            'http://www.ccc.de',
            'http://www.bier.de'
        ];
        $this->storageServiceMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(FileStorageItem::create($key, json_encode($urlStringArray)));
        
        $destinations = $this->subject->getDestinations();
        
        $this->assertEquals(UrlCollection::createFromArray($urlStringArray), $destinations);
    }

    public function testGetDomainHistory()
    {
        $key = 'domainHistory';
        $urlStringArray = [
            'http://www.ccc.de',
            'http://www.bier.de'
        ];
        $this->storageServiceMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(FileStorageItem::create($key, json_encode($urlStringArray)));
        
        $domainHistory = $this->subject->getDomainHistory();
        
        $this->assertEquals(UrlCollection::createFromArray($urlStringArray), $domainHistory);
    }

    public function testGetScraperHistory()
    {
        $key = 'scraperHistory';
        $urlStringArray = [
            'http://www.ccc.de',
            'http://www.bier.de'
        ];
        $this->storageServiceMock->expects($this->once())
            ->method('get')
            ->with($key)
            ->willReturn(FileStorageItem::create($key, json_encode($urlStringArray)));
        
        $scraperHistory = $this->subject->getScraperHistory();
        
        $this->assertEquals(UrlCollection::createFromArray($urlStringArray), $scraperHistory);
    }

    public function testSetScraperHistory()
    {
        $key = 'scraperHistory';
        $scraperHistory = UrlCollection::createFromArray([
            'http://www.ccc.de',
            'http://www.bier.de'
        ]);
        $this->storageServiceMock->expects($this->once())
            ->method('put')
            ->with(FileStorageItem::create($key, json_encode($scraperHistory)));
        
        $this->subject->setScraperHistory($scraperHistory);
    }

    public function testSetDestinations()
    {
        $key = 'destinations';
        $destinations = UrlCollection::createFromArray([
            'http://www.ccc.de',
            'http://www.bier.de'
        ]);
        $this->storageServiceMock->expects($this->once())
            ->method('put')
            ->with(FileStorageItem::create($key, json_encode($destinations)));
        
        $this->subject->setDestinations($destinations);
    }

    public function testSetScraperQueue()
    {
        $key = 'scraperQueue';
        $scraperQueue = UrlQueue::createFromArray([
            'http://www.ccc.de',
            'http://www.bier.de'
        ]);
        $this->storageServiceMock->expects($this->once())
            ->method('put')
            ->with(FileStorageItem::create($key, json_encode($scraperQueue)));
        
        $this->subject->setScraperQueue($scraperQueue);
    }

    public function testSetDomainHistory()
    {
        $key = 'domainHistory';
        $domainHistory = UrlCollection::createFromArray([
            'http://www.ccc.de',
            'http://www.bier.de'
        ]);
        $this->storageServiceMock->expects($this->once())
            ->method('put')
            ->with(FileStorageItem::create($key, json_encode($domainHistory)));
        
        $this->subject->setDomainHistory($domainHistory);
    }

    public function testSetCurrentDomain()
    {
        $key = 'currentDomain';
        $currentDomain = Url::createFromString('http://www.bier.de');
        $this->storageServiceMock->expects($this->once())
            ->method('put')
            ->with(FileStorageItem::create($key, json_encode($currentDomain)));
        
        $this->subject->setCurrentDomain($currentDomain);
    }
}



