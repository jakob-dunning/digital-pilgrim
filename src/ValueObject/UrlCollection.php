<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Library\Ensure;

class UrlCollection implements \IteratorAggregate, \JsonSerializable
{
    use Ensure;

    /** @var array **/
    private $store = [];

    private function __construct()
    {
    }

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

    public static function createFromArray(array $values): self
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

    public function contains(Url $url) : bool
    {
        foreach ($this->store as $storedUrl) {
            if ((string) $url === (string) $storedUrl) {
                return true;
            }
        }
        
        return false;
    }

    public function jsonSerialize()
    {
        return $this->store;
    }

    public function getLastBefore(Url $url) : Url
    {
        $this->ensureIsNotEmptyArray($this->store);
        
        foreach ($this->store as $index => $domain) {
            if ($url->getDomain() === (string) $domain) {
                return $this->store[$index - 1];
            }
        }
    }

    public function containsNormalizedUrl(Url $url) : bool
    {
        foreach ($this->store as $storedUrl) {
            if ((string) $url->getNormalized() === (string) $storedUrl->getNormalized()) {
                return true;
            }
        }
        
        return false;
    }

    public function count(): int
    {
        return count($this->store);
    }
}
