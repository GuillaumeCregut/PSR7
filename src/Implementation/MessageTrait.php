<?php

namespace App\Implementation;

use InvalidArgumentException;

trait MessageTrait
{
    public function withProtocolVersion(string $version): static
    {
        $newMessage = clone $this;
        $newMessage->setProtocolVersion($version);
        return $newMessage;
    }

    public function withHeader(string $name, string | array $value): static
    {
        if(!$this->testKey($name)) {
            throw new InvalidArgumentException('Key is not OK');
        }
        if(is_string($value)) {
            $value=[$value];
        }
        if(!$this->testValue($value)) {
            throw new InvalidArgumentException('Value is not OK');
        }
        $newMessage = clone $this;
        $newMessage->setHeader($name, $value);
        return $newMessage;
    }

    public function withAddedHeader(string $name, string|array $value): static
    {
        if(!$this->testKey($name)) {
            throw new InvalidArgumentException('Key is not OK');
        }
        if(is_string($value)) {
            $value=[$value];
        }
        if(!$this->testValue($value)) {
            throw new InvalidArgumentException('Value is not OK');
        }
        $newMessage = clone $this;
        $newMessage->appendKey($name, $value);
        return $newMessage;
    }

    public function withoutHeader(string $name): static
    {
        $newMessage = clone $this;
        $newMessage->removeKey($name);
        return $newMessage;
    }
}