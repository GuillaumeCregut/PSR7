<?php

namespace App\Implementation;

use InvalidArgumentException;
use App\Interfaces\RequestInterface;


class Request extends AbstractMessage implements RequestInterface
{
   
    public function __construct(array $headers)
    {
        $this->setHeaders($headers);
    }

    public function setProtocolVersion(string $protocol): void
    {
        $versions = ['1.0', '1.1', ];
        if(in_array($protocol,$versions)) {
            $this->protocolVersion = $protocol;
            return ;
        }
        $tempProto = trim(strtoupper($protocol));
        $proto = '';
        if (str_starts_with($tempProto, 'HTTP/')) {
            $proto = explode (' ',substr($tempProto,5))[0];
            if(in_array($proto,$versions)) {
                $this->protocolVersion = $proto;
                return ;
            }
        }
        throw new InvalidArgumentException('Protocol version error');
    }

    public function withProtocolVersion(string $version): static
    {
        $newRequest = clone $this;
        $newRequest->setProtocolVersion($version);
        return $newRequest;
    }
}