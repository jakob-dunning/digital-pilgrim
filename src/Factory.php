<?php
declare(strict_types = 1);
namespace App;

use App\Controller\PilgrimController;
use App\Library\Logger\FileLogger;
use App\Service\Storage\StorageService;
use App\ValueObject\File;
use App\Service\Storage\FilestorageService;
use App\Service\ScraperService;
use App\ValueObject\Path;
use App\Repository\PilgrimRepository;
use App\Controller\MonitorController;

class Factory
{

    public function createPilgrimController()
    {
        return new PilgrimController($this->createFileLogger(), $this->createScraperService(), $this->createPilgrimRepository());
    }

    public function createFileLogger(): FileLogger
    {
        return new FileLogger(File::createFromString(__DIR__ . '/../tmp/error.log'));
    }

    public function createFileStorageService(): StorageService
    {
        return new FilestorageService(Path::createFromString(__DIR__ . '/../fileStorage/'), $this->createFileLogger());
    }

    public function createScraperService(): ScraperService
    {
        return new ScraperService($this->createFileLogger());
    }

    public function createPilgrimRepository(): PilgrimRepository
    {
        return new PilgrimRepository($this->createFileStorageService());
    }

    public function createMonitorController()
    {
        return new MonitorController($this->createPilgrimRepository());
    }
}

