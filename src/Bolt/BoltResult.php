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

namespace Laudis\Neo4j\Bolt;

use function array_splice;
use Bolt\error\MessageException;
use function count;
use Generator;
use function in_array;
use Iterator;
use Laudis\Neo4j\Contracts\FormatterInterface;
use Laudis\Neo4j\Exception\Neo4jException;

/**
 * @psalm-import-type BoltCypherStats from FormatterInterface
 *
 * @implements Iterator<int, list>
 */
final class BoltResult implements Iterator
{
    private BoltConnection $connection;
    private int $fetchSize;
    /** @var list<list> */
    private array $rows = [];
    private ?array $meta = null;
    /** @var list<(callable(array):void)> */
    private array $finishedCallbacks = [];
    private int $qid;

    public function __construct(BoltConnection $connection, int $fetchSize, int $qid)
    {
        $this->connection = $connection;
        $this->fetchSize = $fetchSize;
        $this->qid = $qid;
    }

    public function getFetchSize(): int
    {
        return $this->fetchSize;
    }

    private ?Generator $it = null;

    /**
     * @param callable(array):void $finishedCallback
     */
    public function addFinishedCallback(callable $finishedCallback): void
    {
        $this->finishedCallbacks[] = $finishedCallback;
    }

    /**
     * @return Generator<int, list>
     */
    public function getIt(): Generator
    {
        if ($this->it === null) {
            $this->it = $this->iterator();
        }

        return $this->it;
    }

    /**
     * @return Generator<int, list>
     */
    public function iterator(): Generator
    {
        $i = 0;
        while ($this->meta === null) {
            $this->fetchResults();
            foreach ($this->rows as $row) {
                yield $i => $row;
                ++$i;
            }
        }

        foreach ($this->finishedCallbacks as $finishedCallback) {
            $finishedCallback($this->meta);
        }
    }

    public function consume(): array
    {
        while ($this->valid()) {
            $this->next();
        }

        return $this->meta ?? [];
    }

    private function fetchResults(): void
    {
        try {
            $meta = $this->connection->pull($this->qid, $this->fetchSize);
        } catch (MessageException $e) {
            $this->handleMessageException($e);
        }

        /** @var list<list> $rows */
        $rows = array_splice($meta, 0, count($meta) - 1);
        $this->rows = $rows;

        /** @var array{0: array} $meta */
        if (!array_key_exists('has_more', $meta[0]) || $meta[0]['has_more'] === false) {
            $this->meta = $meta[0];
        }
    }

    /**
     * @return list
     */
    public function current(): array
    {
        return $this->getIt()->current();
    }

    public function next(): void
    {
        $this->getIt()->next();
    }

    public function key(): int
    {
        return $this->getIt()->key();
    }

    public function valid(): bool
    {
        return $this->getIt()->valid();
    }

    public function rewind(): void
    {
        // Rewind is impossible
    }

    public function __destruct()
    {
        if ($this->meta === null && in_array($this->connection->getServerState(), ['STREAMING', 'TX_STREAMING'], true)) {
            $this->discard();
        }
    }

    public function discard(): void
    {
        try {
            $this->connection->discard($this->qid === -1 ? null : $this->qid);
        } catch (MessageException $e) {
            $this->handleMessageException($e);
        }
    }

    /**
     * @throws Neo4jException
     *
     * @return never
     */
    private function handleMessageException(MessageException $e): void
    {
        $exception = Neo4jException::fromMessageException($e);
        if (!($exception->getClassification() === 'ClientError' && $exception->getCategory() === 'Request')) {
            $this->connection->reset();
        }

        throw $exception;
    }
}
