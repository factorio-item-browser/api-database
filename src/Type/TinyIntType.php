<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

/**
 * The type to represent a TINYINT.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TinyIntType extends IntegerType
{
    /**
     * The name of the type.
     */
    public const NAME = 'tinyint';

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
        return 'TINY' . $platform->getIntegerTypeDeclarationSQL($column);
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
