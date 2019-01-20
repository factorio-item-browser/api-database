<?php

namespace FactorioItemBrowser\Api\Database\Repository;

/**
 * The interface that the repository may contain orphans which need a cleanup.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface RepositoryWithOrphansInterface
{
    /**
     * Removes any orphaned from the repository.
     */
    public function removeOrphans(): void;
}
