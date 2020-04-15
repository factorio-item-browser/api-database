<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\EntityType;

/**
 * The enum of entity types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeEntityType extends AbstractEnumType
{
    /**
     * The name of the enum.
     */
    public const NAME = 'enum_entity_type';

    /**
     * The values of the num.
     */
    public const VALUES = [
        EntityType::MOD,
        EntityType::ITEM,
        EntityType::FLUID,
        EntityType::MACHINE,
        EntityType::RECIPE,
    ];
}
