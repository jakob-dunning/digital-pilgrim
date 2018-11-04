<?php
namespace App\ValueObject;

use App\Library\Ensure;

class Path
{
    use Ensure;
    
    private $value;
    
    private function __construct( string $path) {
        $this->ensurePathExists($path);
        
        $this->value = $path;
    }
    
    public static function createFromString(string $path) {
        return new self($path);
    }
    
    public function __toString() {
        return $this->value;
    }
}

