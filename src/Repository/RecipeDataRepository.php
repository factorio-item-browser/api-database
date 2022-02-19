<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;

/**
 * The repository for the RecipeData entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<RecipeData>
 */
class RecipeDataRepository implements
    FindByIdsInterface
{
    /** @use FindByIdsTrait<RecipeData> */
    use FindByIdsTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return RecipeData::class;
    }

    protected function extendQueryForFindByIds(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addSelect("{$alias}i", "{$alias}ii", "{$alias}p", "{$alias}pi")
                     ->leftJoin("{$alias}.ingredients", "{$alias}i")
                     ->leftJoin("{$alias}i.item", "{$alias}ii")
                     ->leftJoin("{$alias}.products", "{$alias}p")
                     ->leftJoin("{$alias}p.item", "{$alias}pi");
    }
}
