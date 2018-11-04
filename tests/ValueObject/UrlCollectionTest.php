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
    
    public function testContains() {
        $urlString = 'https://www.taz.de';
        $urlCollection = UrlCollection::create();
        $urlCollection->add(Url::createFromString($urlString));
        
        $this->assertTrue($urlCollection->contains(Url::createFromString($urlString)));
    }
    
    public function testContainsNot() {
        $urlString = 'https://www.taz.de';
        $urlCollection = UrlCollection::create();
        $urlCollection->add(Url::createFromString($urlString));
        
        $this->assertFalse($urlCollection->contains(Url::createFromString('http://www.zeit.de')));
    }
    
    public function testJsonSerialize() {
        $urlString = 'https://www.taz.de';
        $urlCollection = UrlCollection::create();
        $urlCollection->add(Url::createFromString($urlString));
        
        $this->assertSame('["https:\/\/www.taz.de"]', json_encode($urlCollection));
    }
}

