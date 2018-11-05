<?php
declare(strict_types = 1);
namespace Tests\ValueObject;

use App\ValueObject\File;
use PHPUnit\Framework\TestCase;

/**
 * @covers App\ValueObject\File
 */
class FileTest extends TestCase
{

    public function testCreateFromStringReturnsObjectWithExistingFile()
    {
        $filePathString = __DIR__ . '/../../resources/existingFile.txt';
        $file = File::createFromString($filePathString);
        
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame($filePathString, $file->getPath());
    }

    public function testCreateFromStringThrowsExceptionWithNonexistentFile()
    {
        $filePathString = __DIR__ . '/../../resources/nonExistingFile.txt';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Path could not be found: ' . $filePathString);
        
        File::createFromString($filePathString);
    }
}

