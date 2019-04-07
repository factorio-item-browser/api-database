<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;

/**
 * The repository class of the item database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemRepository extends AbstractRepository implements RepositoryWithOrphansInterface
{
    /**
     * Finds the items with the specified types and names.
     * @param array|string[][] $namesByTypes
     * @param array|int[] $modCombinationIds
     * @return array|Item[]
     */
    public function findByTypesAndNames(array $namesByTypes, array $modCombinationIds = []): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Item::class, 'i');

        $index = 0;
        $conditions = [];
        foreach ($namesByTypes as $type => $names) {
            $conditions[] = '(i.type = :type' . $index . ' AND i.name IN (:names' . $index . '))';
            $queryBuilder->setParameter('type' . $index, $type)
                         ->setParameter('names' . $index, array_values($names));
            ++$index;
        }

        $result = [];
        if ($index > 0) {
            $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');

            if (count($modCombinationIds) > 0) {
                $queryBuilder->innerJoin('i.modCombinations', 'mc')
                             ->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }
            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Finds the items with the specified ids.
     * @param array|int[] $ids
     * @return array|Item[]
     */
    public function findByIds(array $ids): array
    {
        $result = [];
        if (count($ids) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('i')
                         ->from(Item::class, 'i')
                         ->andWhere('i.id IN (:ids)')
                         ->setParameter('ids', array_values($ids));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Finds the items matching the specified keywords.
     * @param array|string[] $keywords
     * @param array|int[] $modCombinationIds
     * @return array|Item[]
     */
    public function findByKeywords(array $keywords, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($keywords) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('i')
                         ->from(Item::class, 'i');

            $index = 0;
            foreach ($keywords as $keyword) {
                $queryBuilder->andWhere('i.name LIKE :keyword' . $index)
                             ->setParameter('keyword' . $index, '%' . addcslashes($keyword, '\\%_') . '%');
                ++$index;
            }

            if (count($modCombinationIds) > 0) {
                $queryBuilder->innerJoin('i.modCombinations', 'mc')
                             ->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Finds random items.
     * @param int $numberOfItems
     * @param array|int[] $modCombinationIds
     * @return array|Item[]
     */
    public function findRandom(int $numberOfItems, array $modCombinationIds = []): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select(['i', 'RAND() AS HIDDEN rand'])
                     ->from(Item::class, 'i')
                     ->addOrderBy('rand')
                     ->setMaxResults($numberOfItems);

        if (count($modCombinationIds) > 0) {
            $queryBuilder->innerJoin('i.modCombinations', 'mc')
                         ->andWhere('mc.id IN (:modCombinationIds)')
                         ->setParameter('modCombinationIds', array_values($modCombinationIds));
        }
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Removes any orphaned items, i.e. items no longer used by any recipe or combination.
     */
    public function removeOrphans(): void
    {
        $itemIds = $this->findOrphanedIds();
        if (count($itemIds) > 0) {
            $this->removeIds($itemIds);
        }
    }

    /**
     * Returns the ids of orphaned machines, which are no longer used by any recipe or combination.
     * @return array|int[]
     */
    protected function findOrphanedIds(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i.id AS id')
                     ->from(Item::class, 'i')
                     ->leftJoin('i.modCombinations', 'mc')
                     ->leftJoin(RecipeIngredient::class, 'ri', 'WITH', 'ri.item = i.id')
                     ->leftJoin(RecipeProduct::class, 'rp', 'WITH', 'rp.item = i.id')
                     ->andWhere('mc.id IS NULL')
                     ->andWhere('ri.item IS NULL')
                     ->andWhere('rp.item IS NULL');

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $data) {
            $result[] = (int) $data['id'];
        }
        return $result;
    }

    /**
     * Removes the items with the specified ids from the database.
     * @param array|int[] $itemIds
     */
    protected function removeIds(array $itemIds): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(Item::class, 'i')
                     ->andWhere('i.id IN (:itemIds)')
                     ->setParameter('itemIds', array_values($itemIds));

        $queryBuilder->getQuery()->execute();
    }
}
