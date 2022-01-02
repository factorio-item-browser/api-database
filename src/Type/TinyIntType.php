<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;

/**
 * The type to represent a TINYINT.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TinyIntType extends IntegerType
{
    public function getName(): string
    {
        return CustomTypes::TINYINT;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TINY' . $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
