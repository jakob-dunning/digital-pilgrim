<?php
namespace App\ValueObject;

use App\Library\Ensure;

class UrlCollection implements \IteratorAggregate, \JsonSerializable
{
    use Ensure;

    private $store = [];

    private function __construct()
    {}

    public static function create(): self
    {
        return new self();
    }

    public function add(Url $url)
    {
        $this->store[] = $url;
    }

    public function getRandom(): Url
    {
        $this->ensureIsNotEmptyArray($this->store);
        
        return $this->store[rand(0, count($this->store) - 1)];
    }

    public function createFromArray(array $values): self
    {
        $urlCollection = new self();
        foreach ($values as $value) {
            $urlCollection->add(Url::createFromString($value));
        }
        
        return $urlCollection;
    }

    public function getIterator(): \ArrayIterator
    {
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

    public function jsonSerialize()
    {
        return $this->store;
    }

    public function getLast()
    {
        $this->ensureIsNotEmptyArray($this->store);
        
        return $this->store[count($this->store) - 1];
    }
}