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
    public const TIMESTAMP = 'timestamp';
    public const TINYINT = 'tinyint';
    public const UUID = UuidBinaryType::NAME;
}
