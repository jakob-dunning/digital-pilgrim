<?php declare(strict_types = 1);

namespace App\Service\Storage;

use App\Library\Ensure;
use App\Library\Logger\Logger;
use App\ValueObject\Path;
use App\ValueObject\StorageItem\FileStorageItem;
use App\ValueObject\StorageItem\StorageItem;

class FilestorageService implements StorageService
{
    use Ensure;

    /** @var Path **/
    private $storageFolder;

    /** @var Logger **/
    private $logger;

    public function __construct(Path $storageFolder, Logger $logger)
    {
        $this->storageFolder = $storageFolder;
        $this->logger = $logger;
    }

    public function put(StorageItem $storageItem)
    {
        $filePath = (string) $this->storageFolder . $storageItem->getKey() . '.json';
        $tmpFilePath = (string) $this->storageFolder . '~' . $storageItem->getKey() . '.json';
        
        $this->ensurePathIsWritable((string) $this->storageFolder);
        
        if (file_exists($filePath) === true) {
            $this->ensurePathIsWritable($filePath);
            file_put_contents($tmpFilePath, (string) $storageItem);
            unlink($filePath);
            rename($tmpFilePath, $filePath);
        } else {
            file_put_contents($filePath, (string) $storageItem);
        }
        
        return;
    }

    public function get(string $key): ?StorageItem
    {
        $filePath = (string) $this->storageFolder . $key . '.json';
        $tmpFilePath = (string) $this->storageFolder . '~' . $key . '.json';
        
        try {
            $this->ensureFileIsReadable($filePath);

            return FileStorageItem::create($key, file_get_contents($filePath));
        } catch (\Throwable $e) {
            $this->logger->warning('File could not be found: ' . $filePath);
        }
        
        try {
            $this->ensureFileIsReadable($tmpFilePath);

            return FileStorageItem::create($key, file_get_contents($tmpFilePath));
        } catch (\Throwable $e) {
            $this->logger->warning('File could not be found: ' . $tmpFilePath);
        }
        
        return null;
    }
}
