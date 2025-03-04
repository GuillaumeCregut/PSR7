<?php

namespace App\Implementation;

use App\Interfaces\RequestInterface;


class Request extends AbstractMessage implements RequestInterface
{
   
    public function __construct(array $headers)
    {
        $this->setHeaders($headers);
    }
}