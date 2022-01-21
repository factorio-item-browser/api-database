<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Constant;

use Ramsey\Uuid\Doctrine\UuidBinaryType;

/**
 * The interface holding the custom Doctrine types used in the entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface CustomTypes
{
    public const ENUM_CATEGORY_TYPE = 'enum_category_type';
    public const ENUM_ENERGY_USAGE_UNIT_TYPE = 'enum_energy_usage_unit';
    public const ENUM_ENTITY_TYPE = 'enum_entity_type';
    public const ENUM_ITEM_TYPE = 'enum_item_type';
    public const ENUM_RECIPE_TYPE = 'enum_recipe_type';

    public const TIMESTAMP = 'timestamp';
    public const TINYINT = 'tinyint';
    public const UUID = UuidBinaryType::NAME;
}
