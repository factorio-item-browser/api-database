<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * The type representing a TIMESTAMP.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TimestampType extends DateTimeType
{
    /**
     * The name of the type.
     */
    public const NAME = 'timestamp';

    /**
     * Returns the name of this type.
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Returns the SQL declaration snippet for a field of this type.
     * @param array<mixed> $column
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TIMESTAMP';
    }

    /**
     * Returns whether an SQL comment hint is required.
     * @param AbstractPlatform $platform
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
