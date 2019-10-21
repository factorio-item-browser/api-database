<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\RecipeMode;

/**
 * The enum of recipe modes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeRecipeMode extends AbstractEnumType
{
    /**
     * The name of the enum.
     */
    public const NAME = 'enum_recipe_mode';

    /**
     * The values of the num.
     */
    public const VALUES = [
        RecipeMode::NORMAL,
        RecipeMode::EXPENSIVE,
    ];
}
