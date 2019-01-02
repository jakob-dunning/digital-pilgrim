<?php declare(strict_types = 1);

namespace App\Library;

use function str_replace;

trait Ensure
{

    private function ensureIsFile(string $path)
    {
        if (is_dir($path) === true) {
            throw new \Exception('Location is not a file: ' . $path);
        }
    }

    public function ensurePathExists(string $path)
    {
        if (file_exists($path) !== true) {
            throw new \Exception('Path could not be found: ' . $path);
        }
    }

    public function ensurePathIsWritable(string $path)
    {
        if (is_writable($path) !== true) {
            throw new \Exception('File is not writable: ' . $path);
        }
    }

    public function ensureFileIsReadable(string $path)
    {
        if (is_readable($path) !== true) {
            throw new \Exception('File is not readable: ' . $path);
        }
    }

    public function ensureIsNotEmptyString(string $data)
    {
        if ($data === '') {
            throw new \Exception('String is empty.');
        }
    }

    public function ensureDirExists(string $path)
    {
        $this->ensurePathExists();
        $this->ensureIsDir($path);
    }

    private function ensureIsDir(string $path)
    {
        if (is_dir() !== true) {
            throw new \Exception('Location is not a directory: ' . $path);
        }
    }

    private function ensureIsValidUrl(string $url)
    {
        $path = parse_url($url, PHP_URL_PATH);

        if (is_null($path) === false && $path !== false) {
            $encoded_path = array_map('urlencode', explode('/', $path));
            $url = str_replace($path, implode('/', $encoded_path), $url);
        }
        
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \Exception('Not a valid url: ' . $url);
        }
    }

    public function ensureIsNotEmptyArray(array $array)
    {
        if (count($array) === 0) {
            throw new \Exception('Array is empty');
        }
    }

    public function ensureArrayKeyExists(string $key, array $store)
    {
        if (key_exists($key, $store) === false) {
            throw new \Exception('Array key does not exist: ' . $key);
        }
    }
}
