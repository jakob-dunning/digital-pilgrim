<?php
declare(strict_types = 1);
namespace Tests\ValueObject\StorageItem;

use PHPUnit\Framework\TestCase;
use App\ValueObject\StorageItem\FileStorageItem;

/**
 * @covers \App\ValueObject\StorageItem\FileStorageItem
 */
class FileStorageItemTest extends TestCase
{

    public function testCreate()
    {
        $fileStorageItem = FileStorageItem::create('bingo', '32143234234dsffdffs');
        
        $this->assertInstanceOf(FileStorageItem::class, $fileStorageItem);
    }

    public function testGetKey()
    {
        $key = 'bingo';
        $fileStorageItem = FileStorageItem::create($key, '32143234234dsffdffs');
        
        $this->assertSame($key, $fileStorageItem->getKey());
    }

    public function testToString()
    {
        $value = 'lkwahföheöohjöewofhöa';
        $fileStorageItem = FileStorageItem::create('bingo', $value);
        
        $this->assertSame($value, (string) $fileStorageItem);
    }
}

