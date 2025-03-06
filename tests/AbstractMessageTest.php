<?php

use App\Message;
use PHPUnit\Framework\TestCase;
use App\Interfaces\StreamInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Message::class)]
class AbstractMessageTest extends TestCase
{
    public function testGetProtocolVersion()
    {
        $message = new Message([]);
        $message->setProtocolVersion('1.0');
        $this->assertEquals('1.0',$message->getProtocolVersion());
        $message->setProtocolVersion('HTTP/1.0');
        $this->assertEquals('1.0',$message->getProtocolVersion());
        $this->expectException(InvalidArgumentException::class);
        $message->setProtocolVersion('sdsqTP/1.0');
        
    }

    public function testGetHeaders()
    {
        $message = new Message([]);
        $this->assertIsArray($message->getHeaders());
    }

    public function testHasHeaderWithNoHeaders()
    {
        $message = new Message([]);
        $this->assertFalse($message->hasHeader('test'));
    }

    public function testHasHeader()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur1', 'valeur2');
        $message = new Message($headers);
        $this->assertTrue($message->hasHeader('test'));
        $this->assertTrue($message->hasHeader('Test'));
        $this->assertFalse($message->hasHeader('toto'));
    }

    public function testGetheaderWithNoHeader()
    {
        $message = new Message([]);
        $this->assertIsArray($message->getHeader('toto'));
        $this->assertEquals(0, count($message->getHeader('toto')));
    }

    public function testGetHeader()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $result = $message->getHeader('test');
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
        $message = new Message($headers);
        $this->assertArrayNotHasKey($badKey1,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey2,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey3,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey4,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey5,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey6,$message->getHeaders());
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
        $message = new Message($headers);
        $this->assertArrayNotHasKey($badKey1,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey2,$message->getHeaders());
        $this->assertArrayNotHasKey($badKey3,$message->getHeaders());
        $this->assertArrayHasKey($goodKey, $message->getHeaders());
    }

    public function testGetHeaderLine()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $this->assertEquals('', $message->getHeaderLine('none'));
        $this->assertEquals('valeur1,valeur2', $message->getHeaderLine('test'));
    }

    public function testWithProtocol()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message->setProtocolVersion('1.0');
        $message2 = $message->withProtocolVersion('1.1');
        $this->assertEquals('1.1', $message2->getProtocolVersion());
        $this->assertEquals('1.0', $message->getProtocolVersion());
        $this->assertEquals($message->getHeaders(), $message2->getHeaders());
    }

    public function testWithProtocolError()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message->setProtocolVersion('1.0');
        $this->expectException(InvalidArgumentException::class);
        $message2 = $message->withProtocolVersion('1.3');
    }

    public function testWithHeaderBadKey()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $badKey1 = "Hello\0world";
        $this->expectException(InvalidArgumentException::class);
        $message->withHeader($badKey1, 'Bonjour');
    }

    public function testWithHeaderBadValue()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $this->expectException(InvalidArgumentException::class);
        $message->withHeader('toto', "Bon\0jour");
    }

    public function testWithHeaderNewKey()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message2 = $message->withHeader('toto', "Bonjour");
        $this->assertEquals("Bonjour",$message2->getHeaderLine('toto'));
        $this->assertEquals("",$message->getHeaderLine('toto'));
    }

    public function testWithHeaderNewKeyArrayValues()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message2 = $message->withHeader('toto', array("Bonjour", 'monde'));
        $this->assertEquals("Bonjour,monde",$message2->getHeaderLine('toto'));
        $this->assertEquals("",$message->getHeaderLine('toto'));
    }

    public function testwithAddedHeaderBadKey()
    {
        $headers['test']=array('valeur1', 'valeur2');
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $badKey1 = "Hello\0world";
        $this->expectException(InvalidArgumentException::class);
        $message->withAddedHeader($badKey1, 'Bonjour');
    }

    public function testwithAddedHeaderBadValue()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $this->expectException(InvalidArgumentException::class);
        $message->withAddedHeader('toto', "Bon\0jour");
    }

    public function testWithAddedHeaderSimpleValueAdded()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message2 = $message->withAddedHeader('toto', "Bonjour");
        $this->assertArrayHasKey('toto',$message2->getHeaders());
        $this->assertArrayNotHasKey('toto', $message->getHeaders());
    }

    public function testWithAddedHeaderSimpleValueppend()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message2 = $message->withAddedHeader('test', "Bonjour");
        $this->assertEquals('valeur1,valeur2,Bonjour',$message2->getHeaderLine('test'));
        $this->assertArrayNotHasKey('toto', $message->getHeaders());
    }

    public function testwithoutHeader()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $message2 = $message->withoutHeader('test');
        $this->assertArrayHasKey('test',$message->getHeaders());
        $this->assertArrayNotHasKey('test', $message2->getHeaders());
    }

    public function testGetBody()
    {
        $headers['test']=array('valeur1', "valeur2");
        $headers['titi']=array('valeur3', 'valeur4');
        $message = new Message($headers);
        $body = $this->createMock(StreamInterface::class);
        $message->setBody($body);
        $this->assertInstanceOf(StreamInterface::class, $message->getBody());
    }
}