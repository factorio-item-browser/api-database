<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Ramsey\Uuid\UuidInterface;

/**
 * The interface signaling the availability of a findAll() method in the repository.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 */
interface FindAllInterface
{
    /**
     * Find all entities.
     * @return array<TEntity>
     */
    public function findAll(UuidInterface $combinationId, int $numberOfResults, int $indexOfFirstResult): array;
}
