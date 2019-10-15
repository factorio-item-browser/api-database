<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\RecipeMode;

/**
 * The enum type representing the recipe mode.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumRecipeMode extends AbstractEnumType
{
    /**
     * The name of the type.
     */
    public const NAME = 'enum_recipe_mode';

    /**
     * Returns the values of the enum type.
     * @return array|string[]
     */
    protected function getValues(): array
    {
        return [
            RecipeMode::NORMAL,
            RecipeMode::EXPENSIVE,
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
