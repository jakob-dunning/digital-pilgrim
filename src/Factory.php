<?php
declare(strict_types = 1);
namespace App;

use App\Entity\Pilgrim;
use App\Library\Logger\FileLogger;
use App\Service\Storage\StorageService;
use App\ValueObject\File;
use App\Service\Storage\FilestorageService;
use App\Service\ScraperService;
use App\ValueObject\Path;

class Factory
{

    public function createPilgrim() : Pilgrim
    {
        return new Pilgrim($this->createFileLogger(), $this->createScraperService(), $this->createRepository());
    }

    public function createFileLogger() : FileLogger
    {
        return new FileLogger(File::createFromString(__DIR__ . '/../tmp/error.log'));
    }

    public function createFileStorageService(): StorageService
    {
        return new FilestorageService(Path::createFromString(__DIR__ . '/../fileStorage/'), $this->createFileLogger());
    }

    public function createScraperService() : ScraperService
    {
        return new ScraperService($this->createFileLogger());
    }

    public function createRepository() : Repository
    {
        return new Repository($this->createFileStorageService());
    }
}

