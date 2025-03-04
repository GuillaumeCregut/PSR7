<?php

use App\Implementation\Request;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Request::class)]
class RequestTest extends TestCase
{

    /* Nécessite une implémentation plus poussée du setting des headers */
    // public function testGetHeaders()
    // {
    //     $request = new Request();
    //     $this->assertEquals(2,$request->getHeaders());
    // }

    public function testHasHeader()
    {
        $request = new Request();
        $headers = [];
        $request->headers = $headers;
        $this->assertFalse($request->hasHeader('test'));
        $headers['test']=array('valeur1, valeur2');
        $headers['titi']=array('valeur1, valeur2');
        $request->headers = $headers;
        $this->assertTrue($request->hasHeader('test'));
        $this->assertTrue($request->hasHeader('Test'));
        $this->assertFalse($request->hasHeader('toto'));
    }
}