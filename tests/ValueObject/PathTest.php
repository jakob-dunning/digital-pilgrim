<?php
declare(strict_types = 1);
namespace Tests\ValueObject;

use App\ValueObject\Path;
use PHPUnit\Framework\TestCase;

/**
 * @covers App\ValueObject\Path
 */
class PathTest extends TestCase
{

    public function testCreateFromStringReturnsObjectWithExistingPath()
    {
        $pathString = __DIR__ . '/../resources/';
        $path = Path::createFromString($pathString);
        
        $this->assertInstanceOf(Path::class, $path);
        $this->assertSame($pathString, (string) $path);
    }

    public function testCreateFromStringThrowsExceptionWithNonexistentPath()
    {
        $pathString = __DIR__ . '/../nonExistingFolder/';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Path could not be found: ' . $pathString);
        
        Path::createFromString($pathString);
    }
}

