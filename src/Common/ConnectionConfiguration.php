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

namespace Laudis\Neo4j\Common;

use Laudis\Neo4j\Databags\DatabaseInfo;
use Laudis\Neo4j\Enum\AccessMode;
use Laudis\Neo4j\Enum\ConnectionProtocol;
use Psr\Http\Message\UriInterface;

/**
 * @psalm-immutable
 */
final class ConnectionConfiguration
{
    private string $serverAgent;
    private UriInterface $serverAddress;
    private string $serverVersion;
    private ConnectionProtocol $protocol;
    private AccessMode $accessMode;
    private ?DatabaseInfo $databaseInfo;
    /** @var 's'|'ssc'|'' */
    private string $encryptionLevel;

    /**
     * @param ''|'s'|'ssc' $encryptionLevel
     */
    public function __construct(
        string $serverAgent,
        UriInterface $serverAddress,
        string $serverVersion,
        ConnectionProtocol $protocol,
        AccessMode $accessMode,
        ?DatabaseInfo $databaseInfo,
        string $encryptionLevel
    ) {
        $this->serverAgent = $serverAgent;
        $this->serverAddress = $serverAddress;
        $this->serverVersion = $serverVersion;
        $this->protocol = $protocol;
        $this->accessMode = $accessMode;
        $this->databaseInfo = $databaseInfo;
        $this->encryptionLevel = $encryptionLevel;
    }

    public function getServerAgent(): string
    {
        return $this->serverAgent;
    }

    public function getServerAddress(): UriInterface
    {
        return $this->serverAddress;
    }

    public function getServerVersion(): string
    {
        return $this->serverVersion;
    }

    public function getProtocol(): ConnectionProtocol
    {
        return $this->protocol;
    }

    public function getAccessMode(): AccessMode
    {
        return $this->accessMode;
    }

    public function getDatabaseInfo(): ?DatabaseInfo
    {
        return $this->databaseInfo;
    }

    /**
     * @return ''|'s'|'ssc'
     */
    public function getEncryptionLevel(): string
    {
        return $this->encryptionLevel;
    }
}
