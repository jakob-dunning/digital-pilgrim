<?php declare(strict_types = 1);

namespace App\Entity;

use App\ValueObject\Url;
use App\ValueObject\UrlCollection;
use App\ValueObject\UrlQueue;
use JsonSerializable;

class Pilgrim implements JsonSerializable
{

    /** @var UrlQueue **/
    private $scraperQueue;

    /** @var Url **/
    private $currentDomain;

    /** @var UrlCollection **/
    private $scraperHistory;

    /** @var UrlCollection **/
    private $domainHistory;

    /** @var UrlCollection **/
    private $destinations;

    public function __construct(
        UrlQueue $scraperQueue,
        Url $currentDomain,
        UrlCollection $scraperHistory,
        UrlCollection $destinations,
        UrlCollection $domainHistory
    ) {
        $this->scraperQueue = $scraperQueue;
        $this->currentDomain = $currentDomain;
        $this->scraperHistory = $scraperHistory;
        $this->destinations = $destinations;
        $this->domainHistory = $domainHistory;
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

    public function jsonSerialize()
    {
        return [
            'scraperQueue' => $this->scraperQueue,
            'scraperHistory' => $this->scraperHistory,
            'domainHistory' => $this->domainHistory,
            'currentDomain' => $this->currentDomain,
            'destinations' => $this->destinations,
        ];
    }
}
