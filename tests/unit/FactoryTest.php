<?php
declare(strict_types = 1);
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Factory;
use App\Entity\Pilgrim;
use App\Library\Logger\FileLogger;
use App\Service\Storage\FilestorageService;
use App\Repository;
use App\Service\ScraperService;

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
    
    public function testCreatePilgrim()
    {
        $pilgrim = $this->subject->createPilgrim();
        
        $this->assertInstanceOf(Pilgrim::class, $pilgrim);
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
    
    public function testCreateRepository()
    {
        $repository = $this->subject->createRepository();
        
        $this->assertInstanceOf(Repository::class, $repository);
    }
    
    public function testCreateScraperService()
    {
        $scraperService = $this->subject->createScraperService();
        
        $this->assertInstanceOf(ScraperService::class, $scraperService);
    }
}