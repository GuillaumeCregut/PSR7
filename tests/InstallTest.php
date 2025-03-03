<?php

use App\Request;
use PHPUnit\Framework\TestCase;

class InstallTest extends TestCase
{
    public function testInstall()
    {
        $request = new Request();
        $this->assertEquals(2,$request->test());
    }
}