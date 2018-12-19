<?php

declare(strict_types = 1);
namespace App\Service\Storage;

use App\ValueObject\StorageItem\StorageItem;

interface StorageService
{

    public function get(string $key): ?StorageItem;

    public function put(StorageItem $storageItem);
}

