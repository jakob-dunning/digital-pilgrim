<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Library\Ensure;

class Url implements \JsonSerializable
{
    use Ensure;

    /** @var string **/
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

    public function getNormalized() : string
    {
        $host = parse_url($this->value, PHP_URL_HOST);

        if (strpos($host, '.www') === 0) {
            $host = substr($host, 4);
        }
        
        return 'http://' . $host;
    }
}
