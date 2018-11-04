<?php
namespace App\Service;

use App\Library\Logger\Logger;
use App\ValueObject\Url;
use App\ValueObject\UrlCollection;

class ScraperService
{

    private $logger;

    private const UNWANTED_FILE_TYPE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'tif',
        'tiff',
        'txt',
        'rtf',
        'avi',
        'mp4',
        'mpg',
        'mp3',
        'swf',
        'gif',
        'png',
        'pdf',
        'tgz',
        'js',
        'xml',
        'zip',
        'gz',
        'doc',
        'docx',
        'odt',
        'xls'
    ];

    private const WANTED_FILE_TYPE_EXTENSIONS = [
        'html',
        'html',
        'shtml',
        'php',
        'asp',
        'jsp',
        'pl'
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
        // timeout? or no redirects?
        $response = curl_exec($curl);
        
        preg_match_all('/<a [. ]*href=[\'\"](.*)[\'\" ]/U', $response, $matches, PREG_PATTERN_ORDER);
        
        $urlCollection = UrlCollection::create();
        
        foreach ($matches[1] as $match) {
            if (true === $this->containsUnDesiredScheme($match)) {
                continue;
            }
            
            if (true === $this->isFile($match)) {
                continue;
            }
            
            if (true === $this->isMailToLink($match)) {
                continue;
            }
            
            if (false === $this->containsScheme($match)) {
                $match = $this->addDomain($match, $currentDomain);
            }
            try {
                $urlCollection->add(Url::createFromString($match));
            } catch (\Exception $e) {
                $this->logger->error('Could not create Url: ' . $e);
            }
        }
        
        return $urlCollection;
    }

    private function containsUnDesiredScheme(string $url)
    {
        if (false === $this->containsScheme($url)) {
            return false;
        }
        
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return false;
        }
        
        return true;
    }

    private function containsScheme(string $url)
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

    private function isFile(string $url)
    {
        $positionOfLastDot = strrpos($url, '.');
        $charactersAfterLastDot = strlen($url) - $positionOfLastDot - 1;
        
        if (false === $positionOfLastDot) {
            return false;
        }
        
        if ($charactersAfterLastDot > 5) {
            return false;
        }
        
        if (in_array(strtolower(substr($url, $positionOfLastDot + 1)), self::WANTED_FILE_TYPE_EXTENSIONS)) {
            return false;
        }
        
        if (in_array(strtolower(substr($url, $positionOfLastDot + 1)), self::UNWANTED_FILE_TYPE_EXTENSIONS)) {
            return true;
        }
        
        return false;
    }

    private function isMailToLink(string $url)
    {
        if (strpos($url, 'mailto:') === 0) {
            return true;
        }
        
        return false;
    }
}

