<?php declare(strict_types = 1);

namespace App\ValueObject;

use App\Library\Ensure;

class File
{

    use Ensure;

    /** @var string **/
    private $path;

    private function __construct(string $path)
    {
        $this->ensurePathExists($path);
        
        $this->path = $path;
    }

    public static function createFromString(string $path): self
    {
        return new self($path);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
