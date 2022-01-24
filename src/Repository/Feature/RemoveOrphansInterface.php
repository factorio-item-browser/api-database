<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

/**
 * The interface signaling the availability of a removeOrphans() method in the repository.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface RemoveOrphansInterface
{
    /**
     * Removes any orphaned entities.
     */
    public function removeOrphans(): void;
}
