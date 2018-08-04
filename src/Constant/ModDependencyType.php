<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Constant;

/**
 * The interface holding the types of the mod dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface ModDependencyType
{
    /**
     * The required mod is mandatory.
     */
    public const MANDATORY = 'mandatory';

    /**
     * The required mod is optional.
     */
    public const OPTIONAL = 'optional';
}
