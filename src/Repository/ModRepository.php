<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindAllInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindAllTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;

/**
 * The repository class of the Mod database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindAllInterface<Mod>
 * @implements FindByIdsInterface<Mod>
 */
class ModRepository extends AbstractRepository implements
    FindAllInterface,
    FindByIdsInterface,
    RemoveOrphansInterface
{
    /** @use FindAllTrait<Mod> */
    use FindAllTrait;
    /** @use FindByIdsTrait<Mod> */
    use FindByIdsTrait;
    /** @use RemoveOrphansTrait<Mod> */
    use RemoveOrphansTrait;

    protected function getEntityClass(): string
    {
        return Mod::class;
    }

    protected function extendQueryForFindAll(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addOrderBy("{$alias}.name", 'ASC');
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }
}
