<?php
namespace App\ValueObject\StorageItem;

use App\ValueObject\StorageItem\StorageItem;
use App\Library\Ensure;

class FileStorageItem implements StorageItem
{
    use Ensure;
    
    private $key;

    private $value;
    
    private function __construct(string $key, string $value) {
        $this->ensureIsNotEmptyString($key);
        $this->ensureIsNotEmptyString($value);
        $this->key = $key;
        $this->value = $value;
    }
    
    public static function create(string $key, string $value) { 
        return new self($key, $value);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function __toString() : string
    {   
        return $this->value;
    }
}

