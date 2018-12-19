<?php
declare(strict_types = 1);
namespace App\ValueObject;

use App\Library\Ensure;

class Url implements \JsonSerializable
{
    use Ensure;

    private $value;

    private function __construct(string $value)
    {
        $this->ensureIsValidUrl($value);
        $this->value = $value;
    }

    public static function createFromString(string $value): self
    {
        return new self($value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getDomain(): string
    {
        $urlFragments = parse_url($this->value);
        
        return $urlFragments['scheme'] . '://' . $urlFragments['host'];
    }

    public function getHost()
    {
        return parse_url($this->value, PHP_URL_HOST);
    }

    public function jsonSerialize()
    {
        return $this->value;
    }
    
    public function getNormalized()
    {
        $host = parse_url($this->value, PHP_URL_HOST);
        if(0 === strpos($host, '.www')) {
            $host = substr($host, 4); 
        }
            
        return 'http://' . $host; 
    }
}



