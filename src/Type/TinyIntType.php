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
     * Returns the SQL declaration snippet for a field of this type.
     * @param mixed[] $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TINY' . $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Returns the name of this type.
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns whether an SQL comment hint is required.
     * @param AbstractPlatform $platform
     * @return boolean
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
