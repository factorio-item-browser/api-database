<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the crafting category database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @extends AbstractIdRepositoryWithOrphans<CraftingCategory>
 * @method CraftingCategory[] findByIds(UuidInterface[] $ids)
 */
class CraftingCategoryRepository extends AbstractIdRepositoryWithOrphans
{
    /**
     * Finds the crafting categories with the specified names.
     * @param array<string> $names
     * @return array<CraftingCategory>
     */
    public function findByNames(array $names): array
    {
        if (count($names) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('cc')
                     ->from(CraftingCategory::class, 'cc')
                     ->where('cc.name IN (:names)')
                     ->setParameter('names', array_values($names));

        return $queryBuilder->getQuery()->getResult();
    }

    protected function getEntityClass(): string
    {
        return CraftingCategory::class;
    }

    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.machines", 'm')
                     ->leftJoin("{$alias}.recipes", 'r')
                     ->andWhere('m.id IS NULL')
                     ->andWhere('r.id IS NULL');
    }
}
