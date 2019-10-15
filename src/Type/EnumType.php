<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * The type representing an enumeration of values.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumType extends Type
{
    /**
     * The name of the type.
     */
    public const NAME = 'enum';

    /**
     * Returns the SQL declaration snippet for a field of this type.
     * @param mixed[] $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return string
     * @throws DBALException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $rawValues = array_filter(explode(',', $fieldDeclaration['values'] ?? ''));
        if (count($rawValues) === 0) {
            throw new DBALException('Missing values option for enum type.');
        }

        $quotedValues = implode(',', array_map(function (string $value) use ($platform): string {
            return $platform->quoteStringLiteral(trim($value));
        }, $rawValues));

        return sprintf('ENUM(%s)', $quotedValues);
    }

    /**
     * Returns the name of this type.
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
