<?php

declare(strict_types=1);

/*
 * This file is part of the Neo4j PHP Client and Driver package.
 *
 * (c) Nagels <https://nagels.tech>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Laudis\Neo4j\Databags;

use Laudis\Neo4j\Contracts\AuthenticateInterface;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
final class ConnectionRequestData
{
    private UriInterface $uri;
    private AuthenticateInterface $auth;
    private string $userAgent;
    private SslConfiguration $config;

    public function __construct(UriInterface $uri, AuthenticateInterface $auth, string $userAgent, SslConfiguration $config)
    {
        $this->uri = $uri;
        $this->auth = $auth;
        $this->userAgent = $userAgent;
        $this->config = $config;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getAuth(): AuthenticateInterface
    {
        return $this->auth;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getSslConfig(): SslConfiguration
    {
        return $this->config;
    }
}
