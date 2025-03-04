<?php

namespace App\Implementation;

use App\Interfaces\RequestInterface;

use function PHPUnit\Framework\isNull;

class Request implements RequestInterface
{
    public array $headers =[];
    
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        if (count($this->headers)===0) {
            return false;
        }
        $result = array_find_key($this->headers, function(array $value, string $key) use ($name) {
            return strtoupper($name) === strtoupper($key);
        });
        return ($result !== null);
    }
}