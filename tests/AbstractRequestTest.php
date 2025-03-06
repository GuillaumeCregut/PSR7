<?php

use App\Interfaces\StreamInterface;
use App\Request;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass(Request::class)]
class AbstractRequestTest extends TestCase
{
    public function testCreateRequestHasHost()
    {
        $headers['test'] = array('valeur1', 'valeur2');
        $headers['titi'] = array('valeur1', 'valeur2');
        $request = new Request('', 'http://foo.com/toto.html', $headers);
        $this->assertEquals('foo.com', $request->getHeaderLine('host'));
    }

    public function testCreateRequestBadURI()
    {
        $headers['test'] = array('valeur1', 'valeur2');
        $headers['titi'] = array('valeur1', 'valeur2');
        $this->expectException(InvalidArgumentException::class);
        $request = new Request('', 'dsfsdfsdf', $headers);
    }

    public function testCreateRequestBadProtocol()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new Request('', 'http://foo.com/toto.html', [], null, 'fr');
    }

    public function testCreateRequestWithBody()
    {
        $body = $this->createMock(StreamInterface::class);
        $request = new Request('', 'http://foo.com/toto.html', [], $body);
        $this->assertInstanceOf(StreamInterface::class,$request->getBody());
    }

    // public function testRequestGetTarget()
    // {
    //     $request = new Request('', 'http://foo.com/toto.html');
    //     $this->assertEquals('toto.html',$request->getRequestTarget());
    // }
}
