<?php declare(strict_types = 1);

namespace App\Service;

use App\Library\Logger\Logger;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;

class ScraperService
{

    /** @var Logger **/
    private $logger;

    private const WANTED_FILE_TYPE_EXTENSIONS = [
        'html',
        'html',
        'shtml',
        'php',
        'asp',
        'jsp',
        'pl',
    ];

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function extractUrls(Url $url, Url $currentDomain): UrlCollection
    {
        $curl = curl_init((string) $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 500);
        // timeout? or no redirects?
        $response = curl_exec($curl);
        
        if ($response === false) {
            return UrlCollection::create();
        }
        
        preg_match_all('/<a [. ]*href=[\'\"](.*)[\'\" ]/U', $response, $matches, PREG_PATTERN_ORDER);
        
        $urlCollection = UrlCollection::create();
        
        foreach ($matches[1] as $url) {
            $url = $this->removeNamedAnchor($url);
            
            if ($this->containsUnDesiredScheme($url) === true) {
                continue;
            }
            
            if ($this->isFile($url) === true) {
                continue;
            }
            
            if ($this->isMailToLink($url) === true) {
                continue;
            }
            
            if ($this->containsScheme($url) === false) {
                $url = $this->addDomain($url, $currentDomain);
            }

            try {
                $urlCollection->add(Url::createFromString($url));
            } catch (\Throwable $e) {
                $this->logger->error('Could not create Url: ' . $e);
            }
        }
        
        return $urlCollection;
    }

    private function containsUnDesiredScheme(string $url) : bool
    {
        if ($this->containsScheme($url) === false) {
            return false;
        }
        
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return false;
        }
        
        return true;
    }

    private function containsScheme(string $url) : bool
    {
        if (preg_match('/^[a-z]*:\/\//', strtolower($url)) !== 1) {
            return false;
        }
        
        return true;
    }

    private function addDomain(string $url, Url $currentDomain): string
    {
        $urlFragments = parse_url((string) $currentDomain);
        
        return $urlFragments['scheme'] . '://' . $urlFragments['host'] . '/' . ltrim($url, '/');
    }

    private function isFile(string $url) : bool
    {
        $url = $this->removeQueryString($url);
        
        $positionOfLastDot = strrpos($url, '.');
        $charactersAfterLastDot = strlen($url) - $positionOfLastDot - 1;
        
        if ($positionOfLastDot === false) {
            return false;
        }
        
        if ($charactersAfterLastDot > 5) {
            return false;
        }
        
        if (in_array(strtolower(substr($url, $positionOfLastDot + 1)), self::WANTED_FILE_TYPE_EXTENSIONS)) {
            return false;
        }
        
        return false;
    }

    private function isMailToLink(string $url) : bool
    {
        if (strpos($url, 'mailto:') === 0) {
            return true;
        }
        
        return false;
    }

    private function removeQueryString(string $url): string
    {
        if (strpos($url, '?') !== false) {
            $url = strstr($url, '?', true);
        }
        
        return $url;
    }

    private function removeNamedAnchor(string $url): string
    {
        if (strpos($url, '#') !== false) {
            $url = strstr($url, '#', true);
        }
        
        return $url;
    }
}
