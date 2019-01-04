<?php
declare(strict_types = 1);
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Factory;
use App\Library\Logger\FileLogger;
use App\Service\Storage\FilestorageService;
use App\Service\ScraperService;
use App\Repository\PilgrimRepository;

/**
 * @covers App\Factory
 */
class FactoryTest extends TestCase
{

    private $subject;

    public function setUp()
    {
        $this->subject = new Factory();
    }

    public function testCreatePilgrimRepository()
    {
        $pilgrim = $this->subject->createPilgrimRepository();
        
        $this->assertInstanceOf(PilgrimRepository::class, $pilgrim);
    }

    public function testCreateFileLogger()
    {
        $fileLogger = $this->subject->createFileLogger();
        
        $this->assertInstanceOf(FileLogger::class, $fileLogger);
    }

    public function testCreateFileStorageService()
    {
        $fileStorageService = $this->subject->createFileStorageService();
        
        $this->assertInstanceOf(FilestorageService::class, $fileStorageService);
    }

    public function testCreateScraperService()
    {
        $scraperService = $this->subject->createScraperService();
        
        $this->assertInstanceOf(ScraperService::class, $scraperService);
    }
}