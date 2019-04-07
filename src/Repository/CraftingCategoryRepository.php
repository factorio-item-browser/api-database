<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;

/**
 * The repository class of the crafting category database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CraftingCategoryRepository extends AbstractRepository implements RepositoryWithOrphansInterface
{
    /**
     * Finds the crafting categories with the specified names.
     * @param array|string[] $names
     * @return array|CraftingCategory[]
     */
    public function findByNames(array $names): array
    {
        $result = [];
        if (count($names) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('cc')
                         ->from(CraftingCategory::class, 'cc')
                         ->andWhere('cc.name IN (:names)')
                         ->setParameter('names', array_values($names));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Removes any orphaned crafting categories, i.e. those no longer used by any recipe or machine.
     */
    public function removeOrphans(): void
    {
        $machineIds = $this->findOrphanedIds();
        if (count($machineIds) > 0) {
            $this->removeIds($machineIds);
        }
    }

    /**
     * Returns the ids of orphaned crafting categories, which are no longer used by any recipe or machine.
     * @return array|int[]
     */
    protected function findOrphanedIds(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('cc.id AS id')
                     ->from(CraftingCategory::class, 'cc')
                     ->leftJoin('cc.machines', 'm')
                     ->leftJoin('cc.recipes', 'r')
                     ->andWhere('m.id IS NULL')
                     ->andWhere('r.id IS NULL');

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $data) {
            $result[] = (int) $data['id'];
        }
        return $result;
    }

    /**
     * Removes the crafting categories with the specified ids from the database.
     * @param array|int[] $craftingCategoryIds
     */
    protected function removeIds(array $craftingCategoryIds): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CraftingCategory::class, 'cc')
                     ->andWhere('cc.id IN (:craftingCategoryIds)')
                     ->setParameter('craftingCategoryIds', array_values($craftingCategoryIds));

        $queryBuilder->getQuery()->execute();
    }
}
