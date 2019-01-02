<?php declare(strict_types = 1);

namespace App\ValueObject\StorageItem;

use App\Library\Ensure;

class FileStorageItem implements StorageItem
{

    use Ensure;

    /** @var string **/
    private $key;

    /** @var string **/
    private $value;

    private function __construct(string $key, string $value)
    {
        $this->ensureIsNotEmptyString($key);
        $this->ensureIsNotEmptyString($value);
        $this->key = $key;
        $this->value = $value;
    }

    public static function create(string $key, string $value) : self
    {
        return new self($key, $value);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
