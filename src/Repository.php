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
        return UrlQueue::createFromArray($this->get('scraperQueue'));
    }

    public function getCurrentDomain(): Url
    {
        return Url::createFromString($this->get('currentDomain'));
    }

    public function getScraperHistory(): UrlCollection
    {
        return UrlCollection::createFromArray($this->get('scraperHistory'));
    }

    public function getDomainHistory(): UrlCollection
    {
        return UrlCollection::createFromArray($this->get('domainHistory'));
    }

    public function getDestinations(): UrlCollection
    {
        return UrlCollection::createFromArray($this->get('destinations'));
    }

    public function setDestinations(UrlCollection $destinations)
    {
        $this->set('destinations', $destinations);  
    }

    public function setScraperHistory(UrlCollection $scraperHistory)
    {
        $this->set('scraperHistory', $scraperHistory);
    }

    public function setDomainHistory(UrlCollection $domainHistory)
    {
        $this->set('domainHistory', $domainHistory);
    }

    public function setScraperQueue(UrlQueue $scraperQueue)
    {
        $this->set('scraperQueue', $scraperQueue);
    }

    public function setCurrentDomain(Url $url)
    {
        $this->set('currentDomain', $url);
    }

    private function get(string $key)
    {
        return json_decode((string) $this->storageService->get($key), true);
    }

    private function set(string $key, \JsonSerializable $data)
    {
        $this->storageService->put(FileStorageItem::create($key, json_encode($data)));
    }
}

