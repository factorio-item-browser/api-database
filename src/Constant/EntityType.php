<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Constant;

/**
 * The interface holding the generic entity types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface EntityType
{
    /**
     * The entity is an actual item you can hold in the hand. Theoretically.
     */
    public const ITEM = 'item';

    /**
     * The entity is actually a fluid. Or a gas.
     */
    public const FLUID = 'fluid';

    /**
     * The entity is a machine. It is actually crafting a recipe.
     */
    public const MACHINE = 'machine';

    /**
     * The entity is a mod.
     */
    public const MOD = 'mod';

    /**
     * The entity is a recipe. It shows how to craft something into something else.
     */
    public const RECIPE = 'recipe';
}
