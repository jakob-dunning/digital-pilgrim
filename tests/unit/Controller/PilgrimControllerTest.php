<?php
declare(strict_types = 1);
namespace Tests\Entity;

use App\Controller\PilgrimController;
use App\Entity\Pilgrim;
use PHPUnit\Framework\TestCase;
use App\ValueObject\UrlQueue;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\Library\Logger\Logger;
use App\Service\ScraperService;
use App\Repository\PilgrimRepository;
use WebSocket;
use App\Library\Config;

/**
 * @covers App\Entity\PilgrimController
 */
class PilgrimControllerTest extends TestCase
{

    private $subject;

    private $loggerMock;

    private $scraperServiceMock;

    private $pilgrimRepositoryMock;

    private $websocketClientMock;

    private $configMock;

    public function setup()
    {
        $this->loggerMock = $this->createMock(Logger::class);
        $this->scraperServiceMock = $this->createMock(ScraperService::class);
        $this->pilgrimRepositoryMock = $this->createMock(PilgrimRepository::class);
        $this->websocketClientMock = $this->createMock(WebSocket\Client::class);
        $this->configMock = $this->createMock(Config::class);
        $this->subject = new PilgrimController(
            $this->loggerMock,
            $this->scraperServiceMock,
            $this->pilgrimRepositoryMock,
            $this->websocketClientMock,
            $this->configMock);
    }

    public function testRunWithNonEmptyScraperQueue()
    {}

    public function testRunWithEmptyScraperQueueNewDestinationFound()
    {}

    public function testRunWithEmptyScraperQueueNewDestinationNotFound()
    {}
}