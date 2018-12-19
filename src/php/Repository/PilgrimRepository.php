<?php
declare(strict_types = 1);
namespace App\Repository;

use App\Service\Storage\StorageService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;
use App\ValueObject\StorageItem\FileStorageItem;
use App\Entity\Pilgrim;

class PilgrimRepository
{

    private $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function getPilgrim(): Pilgrim
    {
        return new Pilgrim(UrlQueue::createFromArray($this->get('scraperQueue')), Url::createFromString($this->get('currentDomain')), UrlCollection::createFromArray($this->get('scraperHistory')), UrlCollection::createFromArray($this->get('destinations')), UrlCollection::createFromArray($this->get('domainHistory')));
    }

    public function persist(Pilgrim $pilgrim)
    {
        $this->set('scraperQueue', $pilgrim->getScraperQueue());
        $this->set('currentDomain', $pilgrim->getCurrentDomain());
        $this->set('scraperHistory', $pilgrim->getScraperHistory());
        $this->set('domainHistory', $pilgrim->getDomainHistory());
        $this->set('destinations', $pilgrim->getDestinations());
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

