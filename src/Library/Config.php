<?php declare(strict_types = 1);

namespace App\Library;

use App\ValueObject\File;

class Config
{
    use Ensure;

    /** @var array **/
    private $store;

    public function __construct(File $configFile)
    {
        $this->ensureFileIsReadable($configFile->getPath());
        
        $this->store = parse_ini_file($configFile->getPath(), true);
    }

    public function get(string $key) : string
    {
        $this->ensureCorrectKeyFormat($key);
        
        $keyFragments = explode('.', $key);
        
        $this->ensureArrayKeyExists($keyFragments[0], $this->store);
        $this->ensureArrayKeyExists($keyFragments[1], $this->store[$keyFragments[0]]);
        
        return $this->store[$keyFragments[0]][$keyFragments[1]];
    }

    public function ensureCorrectKeyFormat(string $key)
    {
        preg_match('/[a-zA-Z0-9]\.[a-zA-Z0-9]/', $key, $matches);
        
        if (count($matches) !== 1) {
            throw new \Exception('Incorrect key format: ' . $key);
        }
    }
}
