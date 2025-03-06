<?php

namespace App;


use InvalidArgumentException;
use App\Interfaces\StreamInterface;
use App\Implementation\MessageTrait;
use App\Implementation\AbstractMessage;



class Message extends AbstractMessage
{
    use MessageTrait;

    

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

    public function setBody(StreamInterface $body)
    {
        $this->body = $body;
    }
}