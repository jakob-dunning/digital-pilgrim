<?php
declare(strict_types = 1);
namespace Tests\ValueObject;

use App\ValueObject\Url;
use PHPUnit\Framework\TestCase;

/**
 * @covers App\ValueObject\Url
 */
class UrlTest extends TestCase
{

    /**
     * @dataProvider validUrlDataProvider
     */
    public function testCreateFromStringReturnsObjectWithValidUrl(string $urlString)
    {
;
        $url = Url::createFromString($urlString);
        
        $this->assertInstanceOf(Url::class, $url);
        $this->assertSame($urlString, (string)$url);
    }

    /**
     * @dataProvider invalidUrlDataProvider
     */
    public function testCreateFromStringThrowsExceptionWithInvalidUrl(string $urlString)
    {
        $urlString = 'lkjadslk';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not a valid url: ' . $urlString);
        
        Url::createFromString($urlString);
    }
    
    public function testGetDomain() {
        $urlString = 'http://sahnekuchen.xxx/bierbauch/d.html';
        $url = Url::createFromString($urlString);
        
        $this->assertSame('http://sahnekuchen.xxx', $url->getDomain());
    }
    
    public function testGetHost() {
        $urlString = 'http://sahnekuchen.xxx/bierbauch/d.html';
        $url = Url::createFromString($urlString);
        
        $this->assertSame('sahnekuchen.xxx', $url->getHost());
    }
    
    public function testJsonSerialize() {
        $urlString = 'http://sahnekuchen.xxx/bierbauch/d.html';
        $url = Url::createFromString($urlString);
        
        $this->assertSame('"http:\/\/sahnekuchen.xxx\/bierbauch\/d.html"', json_encode($url));
    }
    
    public function validUrlDataProvider() {
        return [
            [
                'http://www.ccc.de'
            ],
            [
                'https://www.maba.co.uk'
            ],
            [
                'mailto:hans@pernod.org'
            ],
            [
                'ftp://schnick.fgh/'
            ],
            [
                'ftp://schnick.fgh/rose.html'
            ],
            [
                'ftp://schnack.com/manta'
            ],
            
        ];
    }
    
    public function invalidUrlDataProvider() {
        return [
            [
                'lkjadslk'
            ],
            [
                'http.//ccc.de'
            ],
            [
                'ccc.de'
            ],
            [
                'http:/sdsd.com'
            ],
            [
                '://sdsd.com'
            ],
        ];
    }
}

