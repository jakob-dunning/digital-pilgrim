<?php
namespace App\Service;

use App\Library\logger\Logger;
use App\Service\Storage\StorageService;
use App\ValueObject\StorageItem\FileStorageItem;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;

class DomainService
{

    private $storageService;

    private $scraperService;

    private $logger;

    public function __construct(StorageService $storageService, Logger $logger, ScraperService $scraperService)
    {
        $this->storageService = $storageService;
        $this->logger = $logger;
        $this->scraperService = $scraperService;
    }

    
}

