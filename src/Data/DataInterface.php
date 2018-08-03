<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

/**
 * The interface of the partial data classes.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface DataInterface
{
    /**
     * Returns the order of the data.
     * @return int
     */
    public function getOrder(): int;

    /**
     * Returns the keys to identify identical data.
     * @return array|string[]
     */
    public function getKeys(): array;
}
