<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Category;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;

/**
 * The repository class of the crafting category database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<Category>
 * @implements FindByTypesAndNamesInterface<Category>
 */
class CategoryRepository implements
    FindByIdsInterface,
    FindByTypesAndNamesInterface,
    RemoveOrphansInterface
{
    /** @use FindByIdsTrait<Category> */
    use FindByIdsTrait;
    /** @use FindByTypesAndNamesTrait<Category> */
    use FindByTypesAndNamesTrait;
    /** @use RemoveOrphansTrait<Category> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return Category::class;
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.machines", 'm')
                     ->leftJoin("{$alias}.recipes", 'r')
                     ->andWhere('m.id IS NULL')
                     ->andWhere('r.id IS NULL');
    }
}
