<?php
declare(strict_types = 1);
namespace Tests\ValueObject;

use App\ValueObject\UrlCollection;
use PHPUnit\Framework\TestCase;
use App\ValueObject\Url;

/**
 * @covers App\ValueObject\UrlCollection
 */
class UrlCollectionTest extends TestCase
{

    public function testCreate()
    {
        $urlCollection = UrlCollection::create();
        
        $this->assertInstanceOf(UrlCollection::class, $urlCollection);
    }

    public function testContainsReturnsTrue()
    {
        $urlString = 'https://www.taz.de';
        $urlCollection = UrlCollection::create();
        $urlCollection->add(Url::createFromString($urlString));
        
        $this->assertTrue($urlCollection->contains(Url::createFromString($urlString)));
    }

    public function testContainsReturnsFalse()
    {
        $urlString = 'https://www.taz.de';
        $urlCollection = UrlCollection::create();
        $urlCollection->add(Url::createFromString($urlString));
        
        $this->assertFalse($urlCollection->contains(Url::createFromString('http://www.zeit.de')));
    }

    public function testJsonSerialize()
    {
        $urlString = 'https://www.taz.de';
        $urlCollection = UrlCollection::create();
        $urlCollection->add(Url::createFromString($urlString));
        
        $this->assertSame('["https:\/\/www.taz.de"]', json_encode($urlCollection));
    }

    public function testGetIterator()
    {
        $urlArray = [
            Url::createFromString('https://www.taz.de'),
            Url::createFromString('https://www.zeit.de'),
            Url::createFromString('https://www.bild.de')
        ];
        $urlCollection = UrlCollection::create();
        foreach ($urlArray as $url) {
            $urlCollection->add($url);
        }
        
        $this->assertInstanceOf(\ArrayIterator::class, $urlCollection->getIterator());
        $this->assertEquals(new \ArrayIterator($urlArray), $urlCollection->getIterator());
    }

    public function testGetRandomThrowsExceptionIfEmpty()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Array is empty');
        
        $urlCollection = UrlCollection::create();
        $urlCollection->getRandom();
    }

    public function testGetRandom()
    {
        $urlStringArray = [
            'https://www.taz.de',
            'https://www.zeit.de',
            'https://www.bild.de'
        ];
        $urlCollection = UrlCollection::createFromarray($urlStringArray);
        $randomUrl = $urlCollection->getRandom();
        
        $this->assertInstanceOf(Url::class, $randomUrl);
        $this->assertTrue(in_array((string) $randomUrl, $urlStringArray, true));
    }

    public function testGetLast()
    {
        $urlStringArray = [
            'https://www.taz.de',
            'https://www.zeit.de',
            'https://www.bild.de'
        ];
        $urlCollection = UrlCollection::createFromarray($urlStringArray);
        
        $this->assertEquals(Url::createFromString('https://www.bild.de'), $urlCollection->getLast());
        
        $urlCollection->add(Url::createFromString('http://www.neuezuericher.de'));
        
        $this->assertEquals(Url::createFromString('http://www.neuezuericher.de'), $urlCollection->getLast());
    }
}

