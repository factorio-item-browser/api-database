<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
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
 * @extends AbstractIdRepositoryWithOrphans<Item>
 * @method Item[] findByIds(UuidInterface[] $ids)
 */
class ItemRepository extends AbstractIdRepositoryWithOrphans
{
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
     * @param UuidInterface $combinationId
     * @param NamesByTypes $namesByTypes
     * @return array<Item>
     */
    public function findByTypesAndNames(UuidInterface $combinationId, NamesByTypes $namesByTypes): array
    {
        if ($namesByTypes->isEmpty()) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Item::class, 'i')
                     ->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);

        $index = 0;
        foreach ($namesByTypes->toArray() as $type => $names) {
            $queryBuilder->orWhere("i.type = :type{$index} AND i.name IN (:names{$index})")
                         ->setParameter("type{$index}", $type)
                         ->setParameter("names{$index}", array_values($names));
            ++$index;
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Finds the items matching the specified keywords.
     * @param UuidInterface $combinationId
     * @param array<string> $keywords
     * @return array<Item>
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
     * @return array<Item>
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

    /**
     * Finds all items, sorted by their name.
     * @param UuidInterface $combinationId
     * @return array<Item>
     */
    public function findAll(UuidInterface $combinationId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Item::class, 'i')
                     ->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->addOrderBy('i.name', 'ASC')
                     ->addOrderBy('i.type', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
