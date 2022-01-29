<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindAllInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindAllTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the item database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindAllInterface<Item>
 * @implements FindByIdsInterface<Item>
 * @implements FindByTypesAndNamesInterface<Item>
 */
class ItemRepository implements
    FindAllInterface,
    FindByIdsInterface,
    FindByTypesAndNamesInterface,
    RemoveOrphansInterface
{
    /** @use FindAllTrait<Item> */
    use FindAllTrait;
    /** @use FindByIdsTrait<Item> */
    use FindByIdsTrait;
    /** @use FindByTypesAndNamesTrait<Item> */
    use FindByTypesAndNamesTrait;
    /** @use RemoveOrphansTrait<Item> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return Item::class;
    }

    protected function extendQueryForFindAll(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addOrderBy("{$alias}.name", 'ASC')
                     ->addOrderBy("{$alias}.type", 'ASC');
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->leftJoin(RecipeIngredient::class, 'ri', 'WITH', "ri.item = {$alias}.id")
                     ->leftJoin(RecipeProduct::class, 'rp', 'WITH', "rp.item = {$alias}.id")
                     ->andWhere('c.id IS NULL')
                     ->andWhere('ri.item IS NULL')
                     ->andWhere('rp.item IS NULL');
    }

    /**
     * Finds the items matching the specified keywords.
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

        /** @var array<Item> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * Finds random items.
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

        /** @var array<Item> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
