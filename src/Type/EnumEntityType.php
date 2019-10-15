<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\EntityType;

/**
 * The enum type representing the entity type.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumEntityType extends AbstractEnumType
{
    /**
     * The name of the type.
     */
    public const NAME = 'enum_entity_type';

    /**
     * Returns the values of the enum type.
     * @return array|string[]
     */
    protected function getValues(): array
    {
        return [
            EntityType::MOD,
            EntityType::ITEM,
            EntityType::FLUID,
            EntityType::MACHINE,
            EntityType::RECIPE,
        ];
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
