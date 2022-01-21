<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Common\Constant\RecipeType;

/**
 * The enum of recipe types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeRecipeType extends AbstractEnumType
{
    public const NAME = CustomTypes::ENUM_RECIPE_TYPE;
    public const VALUES = [
        'recipe',
        'mining',
        'rocket-launch',
    ];
}
