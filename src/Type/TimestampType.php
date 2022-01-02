<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;

/**
 * The type representing a TIMESTAMP.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TimestampType extends DateTimeType
{
    public function getName(): string
    {
        return CustomTypes::TIMESTAMP;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TIMESTAMP';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
