<?php
declare(strict_types = 1);
namespace App\Entity;

use App\Library\logger\Logger;
use App\Service\ScraperService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\Repository;
use App\ValueObject\UrlQueue;

class Pilgrim
{

    private $logger;

    private $scraperService;

    private $repository;

    private $scraperQueue;

    private $currentDomain;

    private $scraperHistory;

    private $domainHistory;

    private $destinations;

    public function __construct(Logger $logger, ScraperService $scraperService, Repository $repository)
    {
        $this->logger = $logger;
        $this->scraperService = $scraperService;
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
            $currentUrl = $this->scraperQueue->dequeue();
            
            $scrapedUrls = $this->scraperService->extractUrls($currentUrl, $this->currentDomain);
            $this->addUrlsToScraperQueue($this->extractLocalUrls($scrapedUrls));
            $this->addUrlsToDestinations($this->extractExternalUrls($scrapedUrls));
            
            $this->scraperHistory->add($currentUrl);
            $this->persist();
            return;
        }
        
        try {
            $destination = $this->destinations->getRandom();
        } catch (\Exception $e) {
            $destination = $this->domainHistory->getPrevious();
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

    private function extractLocalUrls(UrlCollection $scrapedUrls): UrlCollection
    {
        $localUrls = UrlCollection::create();
        foreach ($scrapedUrls as $url) {
            if (true === $this->isLocalUrl($url)) {
                $localUrls->add($url);
            }
        }
        
        return $localUrls;
    }

    private function extractExternalUrls(UrlCollection $scrapedUrls): UrlCollection
    {
        $externalUrls = UrlCollection::create();
        foreach ($scrapedUrls as $url) {
            if (false === $this->isLocalUrl($url)) {
                $externalUrls->add($url);
            }
        }
        
        return $externalUrls;
    }

    private function addUrlsToScraperQueue(UrlCollection $urls)
    {
        foreach ($urls as $url) {
            if (false === $this->scraperQueue->contains($url) && false === $this->scraperHistory->contains($url)) {
                $this->scraperQueue->enqueue($url);
            }
        }
    }

    private function addUrlsToDestinations(UrlCollection $urls)
    {
        foreach ($urls as $url) {
            if (false === $this->destinations->contains($url)) {
                $this->destinations->add($url);
            }
        }
    }
}

