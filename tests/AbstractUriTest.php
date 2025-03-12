<?php

use App\Uri;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Uri::class)]
class AbstractUriTest extends TestCase
{
    public function testBadUri()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = new Uri('http:///example.com');
    }
//Scheme testing    
    public function testGetSchemeWithoutScheme()
    {
        $uri = new Uri('/localhost');
        $this->assertEquals('', $uri->getScheme());
    }

    public function testGetScheme()
    {
        $uri = new Uri('http://localhost');
        $this->assertEquals('http', $uri->getScheme());
    }

    public function testGetSchemeWithUpperCase()
    {
        $uri = new Uri('HtTp://localhost');
        $this->assertEquals('http', $uri->getScheme());
    }
//host testing
    public function testGetHostWithoutHost()
    {
        $uri = new Uri('/');
        $this->assertEquals('', $uri->getHost());
    }

    public function testGetHost()
    {
        $uri = new Uri('http://localhost');
        $this->assertEquals('localhost', $uri->getHost());
    }

    public function testGetHostWithUpperCase()
    {
        $uri = new Uri('http://LocalHost');
        $this->assertEquals('localhost', $uri->getHost());
    }
//port testing
    public function testGetPortWithoutPort()
    {
        $uri = new Uri('http://localhost');
        $this->assertEquals(null, $uri->getPort());
    }

    public function testGetPortWithDifPort()
    {
        $uri = new Uri('http://localhost:81');
        $this->assertEquals(81, $uri->getPort());
    }

    public function testGetPortDefautPort()
    {
        $uri = new Uri('http://localhost:80');
        $this->assertEquals(null, $uri->getPort());
    }

    public function testGetPortBadPort()
    {
        $this->expectException(InvalidArgumentException::class);
        $uri = new Uri('http://localhost:-5555555');
    }

    public function testGetPortNoSchemePort()
    {
        $uri = new Uri('/');
        $this->assertEquals(null, $uri->getPort());
    }

    public function testGetPortNoSchemeButPort()
    {
        $uri = new Uri('localhost:80');
        $this->assertEquals(null, $uri->getPort());
    }
//User testing
    public function testGetUserInfoWithoutHost()
    {
        $uri = new Uri('/');
        $this->assertEquals('', $uri->getUserInfo());
        
    }

    public function testGetUserInfoWithoutInfo()
    {
        $uri = new Uri('http://localhost');
        $this->assertEquals('', $uri->getUserInfo());

    }

    public function testGetUserInfoWithUserInfo()
    {
        $uri = new Uri('http://user@localhost');
        $this->assertEquals('user', $uri->getUserInfo());

    }

    public function testGetUserInfoWithPassInfo()
    {
        $uri = new Uri('http://user:pass@localhost');
        $this->assertEquals('user:pass', $uri->getUserInfo());

    }

    public function testGetUserInfoWithSpecialChars()
    {
        $uri = new Uri('http://hello@world:pass@localhost');
        $this->assertEquals('hello%40world:pass', $uri->getUserInfo());
    }
//Authority testing
    public function testgetAuthorityWithoutInfo()
    {
        $uri = new Uri('http://localhost');
        $this->assertEquals('localhost', $uri->getAuthority());
    }

    public function testgetAuthorityWithoutPort()
    {
        $uri = new Uri('http://user:pass@localhost');
        $this->assertEquals('user:pass@localhost', $uri->getAuthority());
    }

    public function testAuthorityWithStrangeScheme()
    {
        $uri = new Uri('xxxxx://user:pass@localhost:80');
        $this->assertEquals('user:pass@localhost:80', $uri->getAuthority());
    }

    public function testAuthorityWithSpecificPort()
    {
        $uri = new Uri('http://user:pass@localhost:81');
        $this->assertEquals('user:pass@localhost:81', $uri->getAuthority());
    }

    public function testAuthorityWithStandardPort()
    {
        $uri = new Uri('http://user:pass@localhost:80');
        $this->assertEquals('user:pass@localhost', $uri->getAuthority());
    }
//Path testing
    public function testPathNoPath()
    {

    }

    public function testPathAbsolute()
    {

    }

    public function testPathRootless()
    {

    }

    public function testPathWithQuery()
    {

    }

    public function testPathWithSpecialChars()
    {
        //better for withPath (fonction interface)
        $uri = new Uri('http://localhost/baz?#€/b%61r');
        // // Query and fragment delimiters and multibyte chars are encoded.
        // self::assertSame('/baz%3F%23%E2%82%AC/b%61r', $uri->getPath());
        $this->assertEquals('/baz%3F%23%E2%82%AC/b%61r', $uri->getPath());
    }

    public function testPathStartWithTwoSlashes()
    {
        //http://toto.com//titi.com =>//titi.com
        //vérifier que si pas d'host=>erreur
    }

//Query testing

//Fragment testing

//With testing


//ToString testing
}