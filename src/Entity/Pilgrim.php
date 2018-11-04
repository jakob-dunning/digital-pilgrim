<?php
declare(strict_types = 1);
namespace App\Entity;

use App\Library\logger\Logger;
use App\Service\ScraperService;
use App\Service\DomainService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\Service\Storage\StorageService;
use App\Repository;
use App\ValueObject\UrlQueue;

class Pilgrim
{

    private $logger;

    private $scraperService;

    private $domainService;

    private $storageService;

    private $repository;

    private $scraperQueue;

    private $currentDomain;

    private $scraperHistory;

    private $domainHistory;

    private $destinations;

    public function __construct(Logger $logger, ScraperService $scraperService, DomainService $websiteService, StorageService $storageService, Repository $repository)
    {
        $this->logger = $logger;
        $this->scraperService = $scraperService;
        $this->domainService = $websiteService;
        $this->storageService = $storageService;
        $this->repository = $repository;
        $this->scraperQueue = $this->repository->getScraperQueue();
        $this->currentDomain = $this->repository->getCurrentDomain();
        $this->scraperHistory = $this->repository->getScraperHistory();
        $this->destinations = $this->repository->getDestinations();
        $this->domainHistory = $this->repository->getDomainHistory();
    }

    public function run()
    {
        if (false === $this->scraperQueue->isEmpty()) {
            echo "ScraperQueue not empty (" . count($this->scraperQueue->getIterator()) . ")\n";
            $currentUrl = $this->scraperQueue->dequeue();
            echo "Scraping  $currentUrl\n";
            $scrapedUrls = $this->scraperService->extractUrls($currentUrl, $this->currentDomain);
            foreach ($scrapedUrls as $url) {
                
                if (true === $this->isLocalUrl($url)) {
                    if (false === $this->scraperQueue->contains($url) && false === $this->scraperHistory->contains($url)) {
                        echo "Adding local Url $url\n";
                        $this->scraperQueue->enqueue($url);
                    }
                    continue;
                }
                if (false === $this->destinations->contains($url)) {
                    echo "Adding destination Url $url\n";
                    $this->destinations->add($url);
                }
            }
            $this->scraperHistory->add($currentUrl);
            $this->persist();
            return;
        }
        
        echo "ScraperQueue empty\n";
        
        try {
            $destination = $this->destinations->getRandom();
        } catch (\Exception $e) {
            $destination = $this->domainHistory->getLast();
        }
        $this->setNewDestination($destination);
        $this->persist();
    }

    private function persist()
    {
        $this->repository->setScraperQueue($this->scraperQueue);
        $this->repository->setCurrentDomain($this->currentDomain);
        $this->repository->setScraperHistory($this->scraperHistory);
        $this->repository->setdomainHistory($this->domainHistory);
        $this->repository->setDestinations($this->destinations);
    }

    private function setNewDestination(Url $destination)
    {
        $this->domainHistory->add($this->currentDomain);
        $this->currentDomain = Url::createFromString($destination->getDomain());
        $this->destinations = UrlCollection::create();
        $this->scraperHistory = UrlCollection::create();
        $this->scraperQueue = UrlQueue::createFromArray([
            $destination->getDomain()
        ]);
    }

    private function isLocalUrl(Url $url): bool
    {
        if (strpos((string) $url, (string) $this->currentDomain) === 0) {
            return true;
        }
        
        return false;
    }
}

