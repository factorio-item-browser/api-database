<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Common\Constant\EntityType;

/**
 * The enum of entity types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeEntityType extends AbstractEnumType
{
    public const NAME = CustomTypes::ENUM_ENTITY_TYPE;
    public const VALUES = [
        EntityType::MOD,

        EntityType::ITEM,
        EntityType::FLUID,
        'resource',

        EntityType::MACHINE,

        'recipe',
        'mining',
        'rocket-launch',

        'technology',
    ];
}
