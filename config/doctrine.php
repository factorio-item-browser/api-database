<?php

declare(strict_types=1);

/**
 * The configuration of the Doctrine integration.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Database;

use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
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
                    UuidBinaryType::NAME => UuidBinaryType::BINARY,
                    'enum' => 'string',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => SimplifiedXmlDriver::class,
                'cache' => 'array',
                'paths' => [
                    'vendor/factorio-item-browser/api-database/config/doctrine' => 'FactorioItemBrowser\Api\Database\Entity',
                ],
            ],
        ],
        'types' => [
            Type\EnumTypeEnergyUsageUnit::NAME => Type\EnumTypeEnergyUsageUnit::class,
            Type\EnumTypeEntityType::NAME => Type\EnumTypeEntityType::class,
            Type\EnumTypeRecipeMode::NAME => Type\EnumTypeRecipeMode::class,
            Type\EnumTypeItemType::NAME => Type\EnumTypeItemType::class,
            Type\TimestampType::NAME => Type\TimestampType::class,
            Type\TinyIntType::NAME => Type\TinyIntType::class,
            UuidBinaryType::NAME => UuidBinaryType::class,
        ],
    ],
];
