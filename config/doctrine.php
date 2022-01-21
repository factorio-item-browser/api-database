<?php

/**
 * The configuration of the Doctrine integration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
// phpcs:ignoreFile

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\Doctrine\UuidBinaryType;

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'numeric_functions' => [
                    'Rand' => Functions\RandFunction::class,
                ],
            ],
        ],
        'connection' => [
            'orm_default' => [
                'doctrine_mapping_types' => [
                    UuidBinaryType::NAME => Types::BINARY,
                    'enum' => 'string',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => AttributeDriver::class,
                'cache' => 'array',
                'paths' => [
                    'vendor/factorio-item-browser/api-database/src/Entity',
                ],
            ],
        ],
        'types' => [
            CustomTypes::ENUM_CATEGORY_TYPE => Type\EnumTypeCategoryType::class,
            CustomTypes::ENUM_ENERGY_USAGE_UNIT_TYPE => Type\EnumTypeEnergyUsageUnit::class,
            CustomTypes::ENUM_ENTITY_TYPE => Type\EnumTypeEntityType::class,
            CustomTypes::ENUM_ITEM_TYPE => Type\EnumTypeItemType::class,
            CustomTypes::ENUM_RECIPE_TYPE => Type\EnumTypeRecipeType::class,
            CustomTypes::TIMESTAMP => Type\TimestampType::class,
            CustomTypes::TINYINT => Type\TinyIntType::class,
            UuidBinaryType::NAME => UuidBinaryType::class,
        ],
    ],
];
