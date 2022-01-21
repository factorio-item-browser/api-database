<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Attribute;

use Attribute;

/**
 * The attribute signaling that the property should be included in the ID calculation.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class IncludeInIdCalculation
{
}
