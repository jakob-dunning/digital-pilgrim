<?php
declare(strict_types = 1);
namespace App\Controller;

use App\Entity\Pilgrim;
use App\Library\Logger\Logger;
use App\Service\ScraperService;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;
use App\Repository\PilgrimRepository;
use App\Library\Config;

class PilgrimController
{

    /** @var Pilgrim **/
    private $pilgrim;

    /** @var Logger **/
    private $logger;

    /** @var ScraperService **/
    private $scraperService;

    /** @var PilgrimRepository **/
    private $pilgrimRepository;

    /** @var WebSocket\Client **/
    private $webSocketClient;

    /** @var Config **/
    private $config;

    public function __construct(
        Logger $logger,
        ScraperService $scraperService,
        PilgrimRepository $pilgrimRepository,
        \WebSocket\Client $webSocketClient,
        Config $config)
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
        if ($this->isScraperQueueSizeWithinBounds() === true) {
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
        } catch (\Throwable $e) {
            $destination = $this->pilgrim->getDomainHistory()->getLastBefore($this->pilgrim->getCurrentDomain());
            $this->logger->warning(
                'No destinations found on ' . $this->pilgrim->getCurrentDomain() . '. Going back to ' . $destination);
        }
        
        $this->setNewDestination($destination);
        $this->pilgrimRepository->persist($this->pilgrim);
        
        if (! $this->config->get('monitor.enabled')) {
            return;
        }
        
        $this->sendToMonitor(json_encode($this->pilgrim));
    }

    private function isScraperQueueSizeWithinBounds(): bool
    {
        return $this->pilgrim->getScraperQueue()->isEmpty() === false
        && $this->pilgrim->getScraperHistory()->count() < $this->config->get('pilgrim.scraperqueue_max_size');
    }

    private function sendToMonitor(string $json)
    {
        try {
            $this->webSocketClient->send($json);
        } catch (\Throwable $e) {
            $this->logger->warning('Websocket client could not send data');
        }
    }

    private function setNewDestination(Url $destination)
    {
        $this->pilgrim->setCurrentDomain(Url::createFromString($destination->getDomain()));
        $this->pilgrim->setDestinations(UrlCollection::create());
        $this->pilgrim->setScraperHistory(UrlCollection::create());
        $this->pilgrim->setScraperQueue(
            UrlQueue::createFromArray([
                $destination->getDomain()
            ]));
    }

    private function isLocalUrl(Url $url): bool
    {
        if (strpos((string) $url, (string) $this->pilgrim->getCurrentDomain()) === 0) {
            return true;
        }
        
        return $this->pilgrim->getCurrentDomain()->getNormalized() === $url->getNormalized();
    }

    private function extractLocalUrls(UrlCollection $scrapedUrls): UrlCollection
    {
        $localUrls = UrlCollection::create();
        
        foreach ($scrapedUrls as $url) {
            if ($this->isLocalUrl($url) !== true) {
                continue;
            }
            
            $localUrls->add($url);
        }
        
        return $localUrls;
    }

    private function extractExternalUrls(UrlCollection $scrapedUrls): UrlCollection
    {
        $externalUrls = UrlCollection::create();
        
        foreach ($scrapedUrls as $url) {
            if ($this->isLocalUrl($url) !== false) {
                continue;
            }
            
            $externalUrls->add($url);
        }
        
        return $externalUrls;
    }

    private function addUrlsToScraperQueue(UrlCollection $urls)
    {
        foreach ($urls as $url) {
            if ($this->isDuplicate($url) === true) {
                continue;
            }
            
            $this->pilgrim->getScraperQueue()->enqueue($url);
        }
    }

    private function isDuplicate(Url $url): bool
    {
        return $this->pilgrim->getScraperQueue()->contains(
            $url) !== false || $this->pilgrim->getScraperHistory()->contains($url) !== false;
    }

    private function addUrlsToDestinations(UrlCollection $urls)
    {
        foreach ($urls as $url) {
            if ($this->pilgrim->getDestinations()->containsNormalizedUrl(
                Url::createFromString($url->getNormalized())) !== false) {
                continue;
            }
            
            $this->pilgrim->getDestinations()->add($url);
        }
    }

    private function ensureDestinationNotExistsInDomainHistory(Url $destination)
    {
        if ($this->pilgrim->getDomainHistory()->contains(Url::createFromString($destination->getDomain()))) {
            throw new \Exception('Domain exists in domain history');
        }
    }
}
