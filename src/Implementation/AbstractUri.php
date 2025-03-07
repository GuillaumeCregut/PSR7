<?php

namespace App\Implementation;

use InvalidArgumentException;
use App\Interfaces\UriInterface;


abstract class AbstractUri implements UriInterface
{

    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';
    private const QUERY_SEPARATORS_REPLACEMENT = ['=' => '%3D', '&' => '%26'];

    private const DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];


    private string  $scheme;
    private string $host;
    private ?int $port = null;
    private string $user;
    private ?string $userPass = null;
    private string $userInfo;
    private string $path;

    public function __construct(string $uri)
    {
        $parser = parse_url($uri);
        if (false === $parser) {
            throw new InvalidArgumentException('Uri is malformed');
        }
        $this->scheme = $this->normalize($parser['scheme'] ?? '');
        $this->host = $this->normalize($parser['host'] ?? '');
        $this->port = $this->parsePort($parser['port'] ?? 0);
        $this->user = $this->parseUser($parser['user'] ?? '');
        $this->userPass = $this->parseUser($parser['pass'] ?? '');
        $this->userInfo = $this->parseUserInfo($this->user, $this->userPass);
        $this->path = $this->ParsePath($parser['path'] ?? '');
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        return ($this->userInfo?$this->userInfo . '@' : '') . $this->host . ($this->port ? ':' . $this->port :'');
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): null | int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        //TODO:  implement
        return $this->path;
    }

    public function getQuery(): string
    {
        //TODO:  implement
        return "test";
    }

    public function getFragment(): string
    {
        //TODO:  implement
        return 'test';
    }

    public function withScheme(string $scheme): static
    {
        //TODO:  implement
        return $this; // temp
    }

    public function withUserInfo(string $user, ?string $password = null): static
    {
        //TODO:  implement
        return $this; // temp
    }

    public function withHost(string $host): static
    {
        //TODO:  implement
        return $this; //temp
    }

    public function withPort(?int $port): static
    {
        //TODO:  implement
        return $this; //temp
    }

    public function withPath(string $path): static
    {
        //TODO:  implement
        return $this; //temp
    }

    public function withQuery(string $query): static
    {
        //TODO:  implement
        return $this; //temp
    }

    public function withFragment(string $fragment): static
    {
        //TODO:  implement
        return $this; //temp
    }

    public function __toString()
    {
        //TODO:  implement
    }

    private function parsePort(int $port): int | null
    {
        if ($port === 0) {
            return null;
        }
        if (!isset($this->scheme) || ('' === $this->scheme)) {
            return null;
        }
        if ($port === self::DEFAULT_PORTS[$this->scheme]) {
            return null;
        }
        return $port;
    }

    private function parseUser(string $userInfo): string
    {
        return preg_replace_callback(
            '/(?:[^%' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ']+|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $userInfo
        );
    }

    private function rawurlencodeMatchZero(array $match): string
    {
        return rawurlencode($match[0]);
    }

    private function normalize(string $value): string
    {
        return strtr($value, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
    }

    private function ParseUserInfo(string $user, ?string $pass): string
    {
        if ('' === $user) {
            return '';
        } else {
            if ((null === $pass) || ('' === $pass)) {
                return $user;
            }
            return $user . ':' . $pass;
        }
    }

    private function ParsePath(string $path): string
    {
        return preg_replace_callback(
            '/(?:[^'.self::CHAR_UNRESERVED.self::CHAR_SUB_DELIMS.'%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawurlencodeMatchZero'],
            $path
        );
    }
}
