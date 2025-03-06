<?php

/**
 * A class that implements the minimal informations required by MessageInterface 
 * from PSR7
 * 
 * in addition with MessageTrait, should be used to implement Request and Response
 * 
 */

namespace App\Implementation;

use App\Interfaces\StreamInterface;
use App\Interfaces\MessageInterface;

abstract class AbstractMessage implements MessageInterface
{
    protected array $headers = [];
    protected string $protocolVersion = '';
    protected StreamInterface $body;

    public function __construct(array $headers)
    {
        $this->setHeaders($headers);
    }

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
        if (count($this->headers) === 0) {
            return false;
        }
        $result = $this->getHeaderKey($name);
        return ($result !== null);
    }

    public function getHeader(string $name): array
    {
        $key = $this->getHeaderKey($name);
        if (is_null($key)) {
            return [];
        }
        return $this->headers[$key];
    }

    public function getHeaderLine(string $name): string
    {
        $key = $this->getHeaderKey($name);
        if (is_null($key)) {
            return '';
        }
        return implode(',', $this->headers[$key]);
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    protected function setHeaders(array $headers): static
    {
        foreach ($headers as $key => $value) {
            if ($this->testKey($key)) {
                if ($this->testValue($value)) {
                    $this->headers[$key] = $value;
                }
            }
        }
        return $this;
    }

    protected function getHeaderKey(string $name): string | null
    {
        $result = array_find_key($this->headers, function (array $value, string $key) use ($name) {
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
        foreach ($values as $value) {
            if ((str_contains($value, "\0") || str_contains($value, "\r") || str_contains($value, "\n"))) {
                return false;
            }
        }
        return true;
    }

    protected function setHeader(string $name, array $value): void
    {
        $key = $this->getHeaderKey($name);
        if (null === $key) {
            $key = $name;
        }
        $this->headers[$key] = $value;
    }

    protected function appendKey(string $name, array $value): void
    {
        $key = $this->getHeaderKey($name);
        if (null === $key) {
            $this->headers[$name] = $value;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $value);
        }
    }

    protected function removeKey(string $name): void
    {
        $key = $this->getHeaderKey($name);
        if (null !== $key) {
            unset($this->headers[$name]);
        }
    }

    protected function changeBody(StreamInterface $body): void
    {
        $this->body = $body;
    }
}
