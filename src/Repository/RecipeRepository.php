<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
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
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the recipe database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindAllInterface<Recipe>
 * @implements FindByIdsInterface<Recipe>
 * @implements FindByTypesAndNamesInterface<Recipe>
 */
class RecipeRepository implements
    FindAllInterface,
    FindByIdsInterface,
    FindByTypesAndNamesInterface,
    RemoveOrphansInterface
{
    /** @use FindAllTrait<Recipe> */
    use FindAllTrait;
    /** @use FindByIdsTrait<Recipe> */
    use FindByIdsTrait;
    /** @use FindByTypesAndNamesTrait<Recipe> */
    use FindByTypesAndNamesTrait;
    /** @use RemoveOrphansTrait<Recipe> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return Recipe::class;
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    protected function extendQueryForFindAll(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addSelect("{$alias}c")
                     ->leftJoin("{$alias}.category", "{$alias}c")
                     ->addOrderBy("{$alias}.type", 'ASC')
                     ->addOrderBy("{$alias}.name", 'ASC');
    }

    protected function extendQueryForFindByTypesAndNames(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addSelect("{$alias}c")
                     ->leftJoin("{$alias}.category", "{$alias}c");
    }

    protected function extendQueryForFindByIds(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addSelect("{$alias}c")
                     ->leftJoin("{$alias}.category", "{$alias}c");
    }

    /**
     * Finds the recipes using one of the provided items as an ingredient.
     * @param array<UuidInterface> $itemIds
     * @return array<Recipe>
     */
    public function findWithIngredients(array $itemIds, ?UuidInterface $combinationId = null): array
    {
        return $this->findByItemIds('ingredients', $itemIds, $combinationId);
    }

    /**
     * Finds the recipes using one of the provided items as a product.
     * @param array<UuidInterface> $itemIds
     * @return array<Recipe>
     */
    public function findWithProducts(array $itemIds, ?UuidInterface $combinationId = null): array
    {
        return $this->findByItemIds('products', $itemIds, $combinationId);
    }

    /**
     * Finds the recipes using one of the provided items.
     * @param string $property
     * @param array<UuidInterface> $itemIds
     * @param UuidInterface|null $combinationId
     * @return array<Recipe>
     */
    protected function findByItemIds(string $property, array $itemIds, ?UuidInterface $combinationId): array
    {
        if (count($itemIds) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('r', 'rc')
                     ->from(Recipe::class, 'r')
                     ->innerJoin('r.category', 'rc')
                     ->innerJoin('r.normalData', 'rn')
                     ->innerJoin("rn.{$property}", 'rni', 'WITH', 'rni.item IN (:itemIds)')
                     ->innerJoin('r.expensiveData', 're')
                     ->innerJoin("re.{$property}", 'rei', 'WITH', 'rei.item IN (:itemIds)')
                     ->setParameter('itemIds', array_map(fn(UuidInterface $id): string => $id->getBytes(), $itemIds));

        if ($combinationId !== null) {
            $queryBuilder->innerJoin('r.combinations', 'c', 'WITH', 'c.id = :combinationId')
                ->setParameter('combinationId', $combinationId, CustomTypes::UUID);
        }

        /** @var array<Recipe> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
