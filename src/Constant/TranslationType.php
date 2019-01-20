<?php

namespace FactorioItemBrowser\Api\Database\Constant;

use FactorioItemBrowser\Common\Constant\EntityType;

/**
 * The interface holding the types of translations.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface TranslationType
{
    /**
     * The translation is of an item.
     */
    public const ITEM = EntityType::ITEM;

    /**
     * The translation is of a fluid or gas.
     */
    public const FLUID = EntityType::FLUID;

    /**
     * The translation is of a machine.
     */
    public const MACHINE = EntityType::MACHINE;

    /**
     * The translation is of a mod.
     */
    public const MOD = 'mod';

    /**
     * The translation is of a recipe.
     */
    public const RECIPE = EntityType::RECIPE;
}
