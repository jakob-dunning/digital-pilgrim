<?php
declare(strict_types = 1);
namespace Tests\Entity;

use App\Controller\PilgrimController;
use App\Entity\Pilgrim;
use PHPUnit\Framework\TestCase;
use App\ValueObject\UrlQueue;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;

/**
 * @covers App\Entity\PilgrimController
 */
class PilgrimControllerTest extends TestCase
{

    private $subject;

    private $pilgrimMock;

    public function setup()
    {
        $this->pilgrimMock = $this->createMock(Pilgrim::class);
        $this->subject = new PilgrimController($this->pilgrimMock);
    }

    public function testRunWithNonEmptyScraperQueue()
    {
        $this->currentDomainMock->method('__toString')->willReturn('http://www.haxenGammel.de');
        $currentUrlMock = Url::createFromString('http://www.haxenGammel.de');
        $scrapedUrlsMock = UrlCollection::createFromArray([
            'http://www.haxenGammel.de/wurst',
            'http://www.haxenGammel.de/mett/',
            'http://www.wurstWaren.de/geil',
            'http://www.haxenGammel.de/räucherKatze/ingo.html',
            'https://neineinneinnein.org'
        ]);
        $this->scraperQueueMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(false);
        
        $this->scraperQueueMock->expects($this->once())
            ->method('deQueue')
            ->willReturn($currentUrlMock);
        
        $this->scraperServiceMock->expects($this->once())
            ->method('extractUrls')
            ->with($currentUrlMock, $this->currentDomainMock)
            ->willReturn($scrapedUrlsMock);
        
        $this->scraperQueueMock->expects($this->exactly(3))
            ->method('contains')
            ->willReturn(false);
        
        $this->scraperHistoryMock->expects($this->exactly(3))
            ->method('contains')
            ->willReturn(false);
        
        $this->scraperQueueMock->expects($this->exactly(3))
            ->method('enqueue');
        
        $this->destinationsMock->expects($this->exactly(2))
            ->method('contains')
            ->willReturn(false);
        
        $this->destinationsMock->expects($this->exactly(2))
            ->method('add');
        
        $this->scraperHistoryMock->expects($this->once())
            ->method('add')
            ->with($currentUrlMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setScraperQueue')
            ->with($this->scraperQueueMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setCurrentDomain')
            ->with($this->currentDomainMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setScraperHistory')
            ->with($this->scraperHistoryMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setdomainHistory')
            ->with($this->domainHistoryMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setDestinations')
            ->with($this->destinationsMock);
        
        $this->subject->run();
    }

    public function testRunWithEmptyScraperQueueNewDestinationFound()
    {
        $randomDestination = 'http://www.random.url';
        
        $this->scraperQueueMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);
        
        $this->destinationsMock->expects($this->once())
            ->method('getRandom')
            ->willreturn(Url::createFromString($randomDestination));
        
        $this->domainHistoryMock->expects($this->once())
            ->method('add')
            ->with($this->currentDomainMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setScraperQueue')
            ->with(UrlQueue::createFromArray([
            $randomDestination
        ]));
        
        $this->repositoryMock->expects($this->once())
            ->method('setCurrentDomain')
            ->with(Url::createFromString($randomDestination));
        
        $this->repositoryMock->expects($this->once())
            ->method('setScraperHistory')
            ->with(UrlCollection::create());
        
        $this->repositoryMock->expects($this->once())
            ->method('setdomainHistory')
            ->with($this->domainHistoryMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setDestinations')
            ->with(UrlCollection::create());
        
        $this->subject->run();
    }

    public function testRunWithEmptyScraperQueueNewDestinationNotFound()
    {
        $previousDestination = 'http://myprevious.url';
        
        $this->scraperQueueMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);
        
        $this->destinationsMock->expects($this->once())
            ->method('getRandom')
            ->willThrowException(new \Exception());
        
        $this->domainHistoryMock->expects($this->once())
            ->method('getLast')
            ->willReturn(Url::createFromString($previousDestination));
        
        $this->domainHistoryMock->expects($this->once())
            ->method('add')
            ->with($this->currentDomainMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setScraperQueue')
            ->with(UrlQueue::createFromArray([
            $previousDestination
        ]));
        
        $this->repositoryMock->expects($this->once())
            ->method('setCurrentDomain')
            ->with(Url::createFromString($previousDestination));
        
        $this->repositoryMock->expects($this->once())
            ->method('setScraperHistory')
            ->with(UrlCollection::create());
        
        $this->repositoryMock->expects($this->once())
            ->method('setdomainHistory')
            ->with($this->domainHistoryMock);
        
        $this->repositoryMock->expects($this->once())
            ->method('setDestinations')
            ->with(UrlCollection::create());
        
        $this->subject->run();
    }
}