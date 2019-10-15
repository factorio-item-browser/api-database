<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * An abstraction of the enum type.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractEnumType extends Type
{
    /**
     * Returns the SQL declaration snippet for a field of this type.
     * @param mixed[] $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = implode(',', array_map(function (string $value) use ($platform): string {
            return $platform->quoteStringLiteral($value);
        }, $this->getValues()));

        return sprintf('ENUM(%s)', $values);
    }

    /**
     * Returns the values of the enum type.
     * @return array|string[]
     */
    abstract protected function getValues(): array;
}

