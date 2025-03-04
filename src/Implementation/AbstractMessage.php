<?php

namespace App\Implementation;

use App\Interfaces\MessageInterface;

abstract class AbstractMessage implements MessageInterface
{
    protected array $headers = [];
    protected string $protocolVersion = '';

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        if (count($this->headers)===0) {
            return false;
        }
        $result = $this->getHeaderKey($name);
        return ($result !== null);
    }

    public function getHeader(string $name): array
    {
        $key=$this->getHeaderKey($name);
        if(is_null($key)) {
            return [];
        }
        return $this->headers[$key];
    }

    public function getHeaderLine(string $name): string
    {
        $key=$this->getHeaderKey($name);
        if(is_null($key)) {
            return '';
        }
        return implode(',', $this->headers[$key]);
    }

    protected function setHeaders(array $headers): static
    {
        foreach($headers as $key=>$value) {
            if ($this->testKey($key)) {
                if($this->testValue($value)) {
                    $this->headers[$key]=$value;
                }
            }
        }
        return $this;
    }

    protected function getHeaderKey(string $name): string | null
    {
        $result = array_find_key($this->headers, function(array $value, string $key) use ($name) {
            return strtoupper($name) === strtoupper($key);
        });
        return $result;
    }

    protected function testKey(string $key): bool
    {
        for ($i = 0; $i < strlen($key); $i++) {
            if (ord($key[$i]) <= 0x20) {
                return false; 
            }
        }
        return true;
    }

    protected function testValue(array $values): bool
    {
        foreach($values as $value) {
            if((str_contains($value, "\0") || str_contains($value, "\r") || str_contains($value, "\n")))
            {
                return false;
            }
        }
        return true;
    }
}