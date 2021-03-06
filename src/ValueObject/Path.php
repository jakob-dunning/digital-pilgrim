<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Library\Ensure;

class Path
{

    use Ensure;

    /** @var string **/
    private $value;

    private function __construct(string $path)
    {
        $this->ensurePathExists($path);
        
        $this->value = $path;
    }

    public static function createFromString(string $path) : self
    {
        return new self($path);
    }

    public function __toString() : string
    {
        return $this->value;
    }
}
