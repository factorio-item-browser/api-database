<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\ItemType;

/**
 * The enum type representing the item type.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumItemType extends AbstractEnumType
{
    /**
     * The name of the type.
     */
    public const NAME = 'enum_item_type';

    /**
     * Returns the values of the enum type.
     * @return array|string[]
     */
    protected function getValues(): array
    {
        return [
            ItemType::ITEM,
            ItemType::FLUID,
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
