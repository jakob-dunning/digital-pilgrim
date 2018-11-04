<?php
namespace App\Service\Storage;

use App\Service\Storage\StorageService;
use App\ValueObject\Path;
use App\ValueObject\StorageItem\StorageItem;
use App\Library\Ensure;
use App\Library\Logger\Logger;
use App\ValueObject\StorageItem\FileStorageItem;

class FilestorageService implements StorageService
{
    use Ensure;

    private $path;

    private $logger;

    public function __construct(Path $storageFolder, Logger $logger)
    {
        $this->storageFolder = $storageFolder;
        $this->logger = $logger;
    }

    public function put(StorageItem $storageItem)
    {
        $filePath = (string) $this->storageFolder . $storageItem->getKey();
        $tmpFilePath = (string) $this->storageFolder . '~' . $storageItem->getKey();
        
        $this->ensurePathIsWritable($this->storageFolder);

        if (true === file_exists($filePath)) {
            $this->ensurePathIsWritable($filePath);
            file_put_contents($tmpFilePath,  (string)$storageItem);
            unlink($filePath);
            rename($tmpFilePath, $filePath);
        } else {
            file_put_contents($filePath,  (string)$storageItem);
        }
        
        
        
        return;
    }

    public function get(string $key): ?StorageItem
    {
        $filePath = (string) $this->storageFolder . $key;
        $tmpFilePath = (string) $this->storageFolder . '~' . $key;
        
        try {
            $this->ensureFileIsReadable($filePath);
            return FileStorageItem::create($key, file_get_contents($filePath));
        } catch (\Exception $e) {
            $this->logger->warning('File could not be found: ' . $filePath);
        }
        
        try {
            $this->ensureFileIsReadable($tmpFilePath);
            return FileStorageItem::create($key, file_get_contents($tmpFilePath));
        } catch (\Exception $e) {
            $this->logger->warning('File could not be found: ' . $tmpFilePath);
        }
        
        return null;
    }
}

