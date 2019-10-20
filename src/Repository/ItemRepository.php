<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the item database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @method array|Item[] findByIds(array|UuidInterface[] $ids)
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
     * Finds the items with the specified type and names.
     * @param UuidInterface $combinationId
     * @param string $type
     * @param array|string[] $names
     * @return array|Item[]
     */
    public function findByTypeAndNames(UuidInterface $combinationId, string $type, array $names): array
    {
        if (count($names) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Item::class, 'i')
                     ->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->andWhere('i.type = :type')
                     ->andWhere('i.name IN (:names)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('type', $type)
                     ->setParameter('names', $names);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Finds the items matching the specified keywords.
     * @param UuidInterface $combinationId
     * @param array|string[] $keywords
     * @return array|Item[]
     */
    public function findByKeywords(UuidInterface $combinationId, array $keywords): array
    {
        if (count($keywords) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Item::class, 'i')
                     ->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);

        foreach (array_values($keywords) as $index => $keyword) {
            $queryBuilder->andWhere("i.name LIKE :keyword{$index}")
                         ->setParameter("keyword{$index}", '%' . addcslashes($keyword, '\\%_') . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Finds random items.
     * @param UuidInterface $combinationId
     * @param int $numberOfItems
     * @return array|Item[]
     */
    public function findRandom(UuidInterface $combinationId, int $numberOfItems): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i', 'RAND() AS HIDDEN rand')
                     ->from(Item::class, 'i')
                     ->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->addOrderBy('rand')
                     ->setMaxResults($numberOfItems);

        return $queryBuilder->getQuery()->getResult();
    }
}
