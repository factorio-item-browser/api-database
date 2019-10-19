<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the item database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @method findByIds(array|UuidInterface[] $ids): array|Item[]
 */
class ItemRepository extends AbstractIdRepositoryWithOrphans
{
    /**
     * Returns the entity class this repository manages.
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Item::class;
    }

    /**
     * Adds the conditions to the query builder for detecting orphans.
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     */
    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->leftJoin(RecipeIngredient::class, 'ri', 'WITH', "ri.item = {$alias}.id")
                     ->leftJoin(RecipeProduct::class, 'rp', 'WITH', "rp.item = {$alias}.id")
                     ->andWhere('c.id IS NULL')
                     ->andWhere('ri.item IS NULL')
                     ->andWhere('rp.item IS NULL');
    }






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
}
