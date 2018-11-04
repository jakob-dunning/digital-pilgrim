<?php
namespace App\ValueObject;

use App\Library\Ensure;

class UrlQueue implements \JsonSerializable
{
    use Ensure;

    private $store = [];

    private function __construct()
    {}

    public static function create(): self
    {
        return new self();
    }

    public function enqueue(Url $url)
    {
        $this->store[] = $url;
    }

    public function dequeue(): Url
    {
        $this->ensureIsNotEmptyArray($this->store);
        
        return array_shift($this->store);
    }

    public function createFromArray(array $values): self
    {
        $urlQueue = new self();
        foreach ($values as $value) {
            $urlQueue->enqueue(Url::createFromString($value));
        }
        
        return $urlQueue;
    }

    public function jsonSerialize()
    {
        return $this->store;
    }

    public function isEmpty()
    {
        if (count($this->store) === 0) {
            return true;
        }
        
        return false;
    }
    
    public function getIterator() : \ArrayIterator {
        return new \ArrayIterator($this->store);
    }
    
    public function contains(Url $url)
    {
        foreach ($this->store as $storedUrl) {
            if ((string)$url === (string)$storedUrl) {
                return true;
            }
        }
        
        return false;
    }
}