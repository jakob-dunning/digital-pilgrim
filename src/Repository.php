<?php
declare(strict_types = 1);
namespace App;

use App\Service\Storage\StorageService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;
use App\ValueObject\StorageItem\FileStorageItem;

class Repository
{

    private $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function getScraperQueue(): UrlQueue
    {
        $values = json_decode((string) $this->storageService->get('scraperQueue'), true);
        $urlQueue = UrlQueue::createFromArray($values);
        
        return $urlQueue;
    }

    public function getCurrentDomain(): Url
    {
        return Url::createFromString((string) $this->storageService->get('currentDomain'));
    }

    public function getScraperHistory(): UrlCollection
    {
        $values = json_decode((string) $this->storageService->get('scraperHistory'), true);
        $urlCollection = UrlCollection::createFromArray($values);
        
        return $urlCollection;
    }

    public function getDomainHistory(): UrlCollection
    {
        $values = json_decode((string) $this->storageService->get('domainHistory'), true);
        $urlCollection = UrlCollection::createFromArray($values);
        
        return $urlCollection;
    }

    public function getDestinations(): UrlCollection
    {
        $values = json_decode((string) $this->storageService->get('destinations'), true);
        $urlList = UrlCollection::createFromArray($values);
        
        return $urlList;
    }

    public function setDestinations(UrlCollection $destinations)
    {
        $this->storageService->put(FileStorageItem::create('destinations', json_encode($destinations)));
    }

    public function setScraperHistory(UrlCollection $scraperHistory)
    {
        $this->storageService->put(FileStorageItem::create('scraperHistory', json_encode($scraperHistory)));
    }
    
    public function setDomainHistory(UrlCollection $domainHistory)
    {
        $this->storageService->put(FileStorageItem::create('domainHistory', json_encode($domainHistory)));
    }

    public function setScraperQueue(UrlQueue $scraperQueue)
    {
        $this->storageService->put(FileStorageItem::create('scraperQueue', json_encode($scraperQueue)));
    }

    public function setCurrentDomain(Url $url)
    {
        $this->storageService->put(FileStorageItem::create('currentDomain', (string) $url));
    }
}

