<?php
namespace App\Library;

use App\ValueObject\File;

trait Ensure
{

    private function ensureIsFile(string $path)
    {
        if (true === is_dir($path)) {
            throw new \Exception('Location is not a file: ' . $path);
        }
    }

    public function ensurePathExists(string $path)
    {
        if (true !== file_exists($path)) {
            throw new \Exception('Path could not be found: ' . $path);
        }
    }

    public function ensurePathIsWritable(string $path)
    {
        if (true !== is_writable($path)) {
            throw new \Exception('File is not writable: ' . $path);
        }
    }

    public function ensureFileIsReadable(string $path)
    {
        if (true !== is_readable($path)) {
            throw new \Exception('File is not readable: ' . $path);
        }
    }

    public function ensureIsNotEmptyString(string $data)
    {
        if ('' === $data) {
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
        if (true !== is_dir()) {
            throw new \Exception('Location is not a directory: ' . $path);
        }
    }

    private function ensureIsValidUrl(string $url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);
        
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Not a valid url: ' . $url);
        }
    }

    public function ensureIsNotEmptyArray(array $array)
    {
        if (count($array) === 0) {
            throw new \Exception('Array is empty');
        }
    }
}

