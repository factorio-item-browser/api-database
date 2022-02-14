<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Ramsey\Uuid\UuidInterface;

/**
 * The interface signaling the availability of a findByNames() method in the repository.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 */
interface FindByNamesInterface
{
    /**
     * Finds the items with the specified types and names.
     * @param array<string> $names
     * @return array<TEntity>
     */
    public function findByNames(array $names, ?UuidInterface $combinationId = null): array;
}
