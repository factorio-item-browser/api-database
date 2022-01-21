<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Api\Database\Constant\CustomTypes;

/**
 * The enum of category types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeCategoryType extends AbstractEnumType
{
    public const NAME = CustomTypes::ENUM_CATEGORY_TYPE;
    public const VALUES = [
        'crafting',
        'resource',
    ];
}
