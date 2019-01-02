<?php
namespace App\Controller;

use App\Library\Logger\Logger;
use App\Service\ScraperService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;
use App\Repository\PilgrimRepository;
use WebSocket;
use App\Library\Config;

class PilgrimController
{

    private $pilgrim;

    private $logger;

    private $scraperService;

    private $pilgrimRepository;

    private $webSocketClient;

    private $config;

    public function __construct(Logger $logger, ScraperService $scraperService, PilgrimRepository $pilgrimRepository, WebSocket\Client $webSocketClient, Config $config)
    {
        $this->logger = $logger;
        $this->scraperService = $scraperService;
        $this->pilgrimRepository = $pilgrimRepository;
        $this->pilgrim = $this->pilgrimRepository->getPilgrim();
        $this->webSocketClient = $webSocketClient;
        $this->config = $config;
    }

    public function run()
    {
        if (false === $this->pilgrim->getScraperQueue()->isEmpty() && $this->pilgrim->getScraperHistory()->count() < $this->config->get('pilgrim.scraperqueue_max_size')) {
            $currentUrl = $this->pilgrim->getScraperQueue()->dequeue();
            
            $scrapedUrls = $this->scraperService->extractUrls($currentUrl, $this->pilgrim->getCurrentDomain());
            $this->addUrlsToScraperQueue($this->extractLocalUrls($scrapedUrls));
            $this->addUrlsToDestinations($this->extractExternalUrls($scrapedUrls));
            
            $this->pilgrim->getScraperHistory()->add($currentUrl);
            $this->pilgrimRepository->persist($this->pilgrim);
                        
            if ($this->config->get('monitor.enabled') === '1') {
                $this->sendToMonitor(json_encode($this->pilgrim));
            }
            
            return;
        }
        $this->pilgrim->getDomainHistory()->add($this->pilgrim->getCurrentDomain());
        try {
            $destination = $this->pilgrim->getDestinations()->getRandom();
            $this->ensureDestinationNotExistsInDomainHistory($destination);
        } catch (\Exception $e) {
            $destination = $this->pilgrim->getDomainHistory()->getLastBefore($this->pilgrim->getCurrentDomain());
            $this->logger->warning('No destinations found on ' . $this->pilgrim->getCurrentDomain() . '. Going back to ' . $destination);
        }
        $this->setNewDestination($destination);
        $this->pilgrimRepository->persist($this->pilgrim);
        
        if ($this->config->get('monitor.enabled')) {
            $this->sendToMonitor(json_encode($this->pilgrim));
        }
    }

    private function sendToMonitor(string $json)
    {
        try {
            $this->webSocketClient->send($json);
        } catch (\Exception $e) {
            $this->logger->warning('Websocket client could not send data');
        }
    }

    private function setNewDestination(Url $destination)
    {
        
        $this->pilgrim->setCurrentDomain(Url::createFromString($destination->getDomain()));
        $this->pilgrim->setDestinations(UrlCollection::create());
        $this->pilgrim->setScraperHistory(UrlCollection::create());
        $this->pilgrim->setScraperQueue(UrlQueue::createFromArray([
            $destination->getDomain()
        ]));
    }

    private function isLocalUrl(Url $url): bool
    {
        if (strpos((string) $url, (string) $this->pilgrim->getCurrentDomain()) === 0) {
            return true;
        }
        
        if ($this->pilgrim->getCurrentDomain()->getNormalized() === $url->getNormalized()) {
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
            if (false === $this->pilgrim->getScraperQueue()->contains($url) && false === $this->pilgrim->getScraperHistory()->contains($url)) {
                $this->pilgrim->getScraperQueue()->enqueue($url);
            }
        }
    }

    private function addUrlsToDestinations(UrlCollection $urls)
    {
        foreach ($urls as $url) {
            if (false === $this->pilgrim->getDestinations()->containsNormalizedUrl(Url::createFromString($url->getNormalized()))) {
                $this->pilgrim->getDestinations()->add($url);
            }
        }
    }
    
    private function ensureDestinationNotExistsInDomainHistory(Url $destination) {
        if($this->pilgrim->getDomainHistory()->contains(Url::createFromString($destination->getDomain()))) {
            throw new \Exception('Domain exists in domain history');
        }
    }
}

