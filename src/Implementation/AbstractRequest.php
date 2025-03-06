<?php

namespace App\Implementation;

use App\Body;
use InvalidArgumentException;
use App\Interfaces\UriInterface;
use App\Interfaces\StreamInterface;
use App\Interfaces\RequestInterface;

abstract class AbstractRequest extends AbstractMessage implements RequestInterface
{
    use MessageTrait;

    protected UriInterface $uri;
    protected string $method;

    public function __construct(string $method, string $uri, array $headers = [], string|StreamInterface|null $body = null, string $protocol = '1.1')
    {
        parent::__construct($headers);
        if (!$this->hasHeader('host')) {
            $host = parse_url($uri,  PHP_URL_HOST);
            if (!$host) {
                throw new InvalidArgumentException('URI is malformed');
            }
            $this->setHeader('host', array($host));
        }
        $this->setProtocolVersion($protocol);
        //Build Uri interface here

        if (!$body instanceof StreamInterface) {
            $body = $this->createBody($body);
        }
        if(!(null === $body)) {
            $this->setBody($body);
        }
    }

    protected function setProtocolVersion(string $protocol): void
    {
        $versions = ['1.0', '1.1',];
        if (in_array($protocol, $versions)) {
            $this->protocolVersion = $protocol;
            return;
        }
        throw new InvalidArgumentException('Protocol version error');
    }

    public function setBody(StreamInterface $body)
    {
        $this->body = $body;
    }

    public function getRequestTarget(): string
    {
        return '';
    }

    private function createBody(?string $body): StreamInterface
    {
        //Manage case body is null
        return new Body();
    }
}
