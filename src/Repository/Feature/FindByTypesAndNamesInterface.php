<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use Ramsey\Uuid\UuidInterface;

/**
 * The interface signaling the availability of a findByTypesAndNames() method in the repository.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 */
interface FindByTypesAndNamesInterface
{
    /**
     * Finds the items with the specified types and names.
     * @return array<TEntity>
     */
    public function findByTypesAndNames(NamesByTypes $namesByTypes, ?UuidInterface $combinationId = null): array;
}
