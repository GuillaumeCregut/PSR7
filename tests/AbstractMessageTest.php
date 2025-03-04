<?php

use App\Implementation\AbstractMessage;
use App\Implementation\Request;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Request::class)]
class AbstractMessageTest extends TestCase
{
    public function testGetProtocolVersion()
    {
        $request = new Request([]);
        $request->setProtocolVersion('1.0');
        $this->assertEquals('1.0',$request->getProtocolVersion());
        $request->setProtocolVersion('HTTP/1.0');
        $this->assertEquals('1.0',$request->getProtocolVersion());
        $this->expectException(InvalidArgumentException::class);
        $request->setProtocolVersion('sdsqTP/1.0');
        
    }

    public function testGetHeaders()
    {
        $request = new Request([]);
        $this->assertIsArray($request->getHeaders());
    }

    public function testHasHeaderWithNoHeaders()
    {
        $request = new Request([]);
        $this->assertFalse($request->hasHeader('test'));
    }

    public function testHasHeader()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur1', 'valeur2');
        $request = new Request($headers);
        $this->assertTrue($request->hasHeader('test'));
        $this->assertTrue($request->hasHeader('Test'));
        $this->assertFalse($request->hasHeader('toto'));
    }

    public function testGetheaderWithNoHeader()
    {
        $request = new Request([]);
        $this->assertIsArray($request->getHeader('toto'));
        $this->assertEquals(0, count($request->getHeader('toto')));
    }

    public function testGetHeader()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $result = $request->getHeader('test');
        $this->assertIsArray($result);
        $this->assertEquals( $headers['test'], $result);
    }

    public function testSettingsHeadersWithIllegalCharInKey()
    {
        $badKey1 = "Hello\0world";
        $badKey2 = "Hello\rworld";
        $badKey3 = "Hello\nworld";
        $badKey4 = "Hel\0lo\nworld\r";
        $badKey5 = "Hello". chr(19) ."world";
        $badKey6 = "Hello world";
        $headers[$badKey1]= array('value1', 'value2');
        $headers[$badKey2]= array('value1', 'value2');
        $headers[$badKey3]= array('value1', 'value2');
        $headers[$badKey4]= array('value1', 'value2');
        $headers[$badKey5]= array('value1', 'value2');
        $headers[$badKey6]= array('value1', 'value2');
        $request = new Request($headers);
        $this->assertArrayNotHasKey($badKey1,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey2,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey3,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey4,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey5,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey6,$request->getHeaders());
    }

    public function testHeadersWithIllegalCharInValues()
    {
        $badKey1 = "test1";
        $badKey2 = "test2";
        $badKey3 = "test3";
        $goodKey = "test4";
        $headers[$badKey1]= array("value1 \n", 'value2');
        $headers[$badKey2]= array('value1', "value2 \r");
        $headers[$badKey3]= array("value1 \0", 'value2');
        $headers[$goodKey]= array("value1 ", 'value2');
        $request = new Request($headers);
        $this->assertArrayNotHasKey($badKey1,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey2,$request->getHeaders());
        $this->assertArrayNotHasKey($badKey3,$request->getHeaders());
        $this->assertArrayHasKey($goodKey, $request->getHeaders());
    }

    public function testGetHeaderLine()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $this->assertEquals('', $request->getHeaderLine('none'));
        $this->assertEquals('valeur1,valeur2', $request->getHeaderLine('test'));
    }

    public function testWithProtocol()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request->setProtocolVersion('1.0');
        $request2 = $request->withProtocolVersion('1.1');
        $this->assertEquals('1.1', $request2->getProtocolVersion());
        $this->assertEquals('1.0', $request->getProtocolVersion());
        $this->assertEquals($request->getHeaders(), $request2->getHeaders());
    }

    public function testWithProtocolError()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request->setProtocolVersion('1.0');
        $this->expectException(InvalidArgumentException::class);
        $request2 = $request->withProtocolVersion('1.3');
    }

    public function testWithHeaderBadKey()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $badKey1 = "Hello\0world";
        $this->expectException(InvalidArgumentException::class);
        $request->withHeader($badKey1, 'Bonjour');
    }

    public function testWithHeaderBadValue()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $this->expectException(InvalidArgumentException::class);
        $request->withHeader('toto', "Bon\0jour");
    }

    public function testWithHeaderNewKey()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request2 = $request->withHeader('toto', "Bonjour");
        $this->assertEquals("Bonjour",$request2->getHeaderLine('toto'));
        $this->assertEquals("",$request->getHeaderLine('toto'));
    }

    public function testWithHeaderNewKeyArrayValues()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request2 = $request->withHeader('toto', array("Bonjour", 'monde'));
        $this->assertEquals("Bonjour,monde",$request2->getHeaderLine('toto'));
        $this->assertEquals("",$request->getHeaderLine('toto'));
    }

    public function testwithAddedHeaderBadKey()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $badKey1 = "Hello\0world";
        $this->expectException(InvalidArgumentException::class);
        $request->withAddedHeader($badKey1, 'Bonjour');
    }

    public function testwithAddedHeaderBadValue()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $this->expectException(InvalidArgumentException::class);
        $request->withAddedHeader('toto', "Bon\0jour");
    }

    public function testWithAddedHeaderSimpleValueAdded()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request2 = $request->withAddedHeader('toto', "Bonjour");
        $this->assertArrayHasKey('toto',$request2->getHeaders());
        $this->assertArrayNotHasKey('toto', $request->getHeaders());
    }

    public function testWithAddedHeaderSimpleValueppend()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request2 = $request->withAddedHeader('test', "Bonjour");
        $this->assertEquals('valeur1,valeur2,Bonjour',$request2->getHeaderLine('test'));
        $this->assertArrayNotHasKey('toto', $request->getHeaders());
    }

    public function testwithoutHeader()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $request = new Request($headers);
        $request2 = $request->withoutHeader('test');
        $this->assertArrayHasKey('test',$request->getHeaders());
        $this->assertArrayNotHasKey('test', $request2->getHeaders());
    }
}