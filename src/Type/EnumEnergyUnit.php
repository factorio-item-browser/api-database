<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Type;

use FactorioItemBrowser\Common\Constant\EnergyUsageUnit;

/**
 * The enum type for the energy usage.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class EnumEnergyUnit extends AbstractEnumType
{
    /**
     * The name of the type.
     */
    public const NAME = 'enum_energy_usage_unit';

    /**
     * Returns the values of the enum type.
     * @return array|string[]
     */
    protected function getValues(): array
    {
        return EnergyUsageUnit::ORDERED_UNITS;
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
