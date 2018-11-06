<?php
declare(strict_types = 1);
namespace App\Entity;

use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;

class Pilgrim
{

    private $scraperQueue;

    private $currentDomain;

    private $scraperHistory;

    private $domainHistory;

    private $destinations;

    public function __construct(UrlQueue $scraperQueue, Url $currentDomain, UrlCollection $scraperHistorry, UrlCollection $destinations, UrlCollection $domainhistory)
    {
        $this->scraperQueue = $scraperQueue;
        $this->currentDomain = $currentDomain;
        $this->scraperHistory = $scraperHistorry;
        $this->destinations = $destinations;
        $this->domainHistory = $domainhistory;
    }

    public function getScraperQueue(): UrlQueue
    {
        return $this->scraperQueue;
    }

    public function setScraperQueue(UrlQueue $scraperQueue)
    {
        $this->scraperQueue = $scraperQueue;
    }

    public function getCurrentDomain(): Url
    {
        return $this->currentDomain;
    }

    public function setCurrentDomain(Url $currentDomain)
    {
        $this->currentDomain = $currentDomain;
    }

    public function getScraperHistory(): UrlCollection
    {
        return $this->scraperHistory;
    }

    public function setScraperHistory(UrlCollection $scraperHistory)
    {
        $this->scraperHistory = $scraperHistory;
    }

    public function getDomainHistory(): UrlCollection
    {
        return $this->domainHistory;
    }

    public function setDomainHistory(UrlCollection $domainHistory)
    {
        $this->domainHistory = $domainHistory;
    }

    public function getDestinations(): UrlCollection
    {
        return $this->destinations;
    }

    public function setDestinations(UrlCollection $destinations)
    {
        $this->destinations = $destinations;
    }
}

