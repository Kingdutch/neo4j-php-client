<?php

/*
 * This file is part of the Neo4j PHP Client and Driver package.
 *
 * (c) Nagels <https://nagels.tech>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Laudis\Neo4j\Enum;

use JsonSerializable;
use Laudis\TypedEnum\TypedEnum;

/**
 * @method static self ENABLE()
 * @method static self DISABLE()
 * @method static self FROM_URL()
 * @method static self ENABLE_WITH_SELF_SIGNED()
 *
 * @extends TypedEnum<string>
 *
 * @psalm-immutable
 *
 * @psalm-suppress MutableDependency
 */
final class SslMode extends TypedEnum implements JsonSerializable
{
    private const ENABLE = 'enable';
    private const ENABLE_WITH_SELF_SIGNED = 'enable_with_self_signed';
    private const DISABLE = 'disable';
    private const FROM_URL = 'from_url';

    public function __toString()
    {
        /** @noinspection MagicMethodsValidityInspection */
        return $this->getValue();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getValue();
    }
}
