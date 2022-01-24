<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Ramsey\Uuid\UuidInterface;

/**
 * The interface signaling the availability of a findByIds() method in the repository.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 */
interface FindByIdsInterface
{
    /**
     * Returns the entities with the specified ids.
     * @param array<UuidInterface> $ids
     * @return array<TEntity>
     */
    public function findByIds(array $ids): array;
}
