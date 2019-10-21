<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\ItemType;

/**
 * The enum of item types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeItemType extends AbstractEnumType
{
    /**
     * The name of the enum.
     */
    public const NAME = 'enum_item_type';

    /**
     * The values of the num.
     */
    public const VALUES = [
        ItemType::ITEM,
        ItemType::FLUID,
    ];
}
