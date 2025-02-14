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

namespace Laudis\Neo4j\Tests\Integration;

use Dotenv\Dotenv;
use function explode;
use function is_string;
use Laudis\Neo4j\Authentication\Authenticate;
use Laudis\Neo4j\Bolt\BoltResult;
use Laudis\Neo4j\Bolt\ProtocolFactory;
use Laudis\Neo4j\Bolt\SslConfigurationFactory;
use Laudis\Neo4j\Bolt\SystemWideConnectionFactory;
use Laudis\Neo4j\BoltFactory;
use Laudis\Neo4j\Common\Uri;
use Laudis\Neo4j\Databags\ConnectionRequestData;
use Laudis\Neo4j\Databags\SessionConfiguration;
use Laudis\Neo4j\Databags\SslConfiguration;
use Laudis\Neo4j\Enum\SslMode;
use PHPUnit\Framework\TestCase;

final class BoltResultIntegrationTest extends TestCase
{
    /**
     * @return array<string, list<string>>
     */
    public function buildConnections(): array
    {
        $connections = $_ENV['NEO4J_CONNECTIONS'] ?? false;
        if (!is_string($connections)) {
            Dotenv::createImmutable(__DIR__.'/../../')->load();
            /** @var string|mixed $connections */
            $connections = $_ENV['NEO4J_CONNECTIONS'] ?? false;
            if (!is_string($connections)) {
                return ['bolt://neo4j:test@neo4j' => ['bolt://neo4j:test@neo4j']];
            }
        }

        $tbr = [];
        foreach (explode(',', $connections) as $connection) {
            $tbr[$connection] = [$connection];
        }

        return $tbr;
    }

    /**
     * @dataProvider  buildConnections
     */
    public function testIterationLong(string $connection): void
    {
        $uri = Uri::create($connection);
        $i = 0;
        $factory = new BoltFactory(
            SystemWideConnectionFactory::getInstance(),
            new ProtocolFactory(),
            new SslConfigurationFactory()
        );
        $connection = $factory->createConnection(
            new ConnectionRequestData($uri, Authenticate::fromUrl($uri), 'a/b', new SslConfiguration(SslMode::FROM_URL(), false)),
            SessionConfiguration::default()
        );

        $connection->getImplementation()[0]->run('UNWIND range(1, 100000) AS i RETURN i');
        $result = new BoltResult($connection, 1000, -1);
        foreach ($result as $i => $x) {
            self::assertEquals($i + 1, $x[0] ?? 0);
        }

        self::assertEquals(100000, $i + 1);
        self::assertIsArray($result->consume());
    }
}
