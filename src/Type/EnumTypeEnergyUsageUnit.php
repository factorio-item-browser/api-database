<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\EnergyUsageUnit;

/**
 * The enum of energy usage units.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumTypeEnergyUsageUnit extends AbstractEnumType
{
    /**
     * The name of the enum.
     */
    public const NAME = 'enum_energy_usage_unit';

    /**
     * The values of the num.
     */
    public const VALUES = [
        EnergyUsageUnit::WATT,
        EnergyUsageUnit::KILOWATT,
        EnergyUsageUnit::MEGAWATT,
        EnergyUsageUnit::GIGAWATT,
        EnergyUsageUnit::TERAWATT,
        EnergyUsageUnit::PETAWATT,
        EnergyUsageUnit::EXAWATT,
        EnergyUsageUnit::ZETTAWATT,
        EnergyUsageUnit::YOTTAWATT,
    ];
}
