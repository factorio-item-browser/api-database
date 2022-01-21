<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Common\Constant\ItemType;

/**
 * The enum of item types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeItemType extends AbstractEnumType
{
    public const NAME = CustomTypes::ENUM_ITEM_TYPE;
    public const VALUES = [
        ItemType::ITEM,
        ItemType::FLUID,
        'resource',
    ];
}
