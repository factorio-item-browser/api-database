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
        $queryBuilder->select('r')
                     ->from(Recipe::class, 'r')
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

    /**
     * Finds the data of the recipes with the specified keywords.
     * @param array<string> $keywords
     * @return array<RecipeData>
     */
    public function findDataByKeywords(UuidInterface $combinationId, array $keywords): array
    {
        if (count($keywords) === 0) {
            return [];
        }

        $columns = [
            'r.id AS id',
            'r.name AS name',
            'r.mode AS mode',
        ];
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select($columns)
                     ->from(Recipe::class, 'r')
                     ->innerJoin('r.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);

        foreach (array_values($keywords) as $index => $keyword) {
            $queryBuilder->andWhere("r.name LIKE :keyword{$index}")
                         ->setParameter("keyword{$index}", '%' . addcslashes($keyword, '\\%_') . '%');
        }

        /** @var array<array{id: UuidInterface, name: string, mode: string}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $this->mapRecipeDataResult($queryResult);
    }

    /**
     * Finds the data of all recipes, sorted by their name.
     * @return array<RecipeData>
     */
    public function findAllData(UuidInterface $combinationId): array
    {
        $columns = [
            'r.id AS id',
            'r.name AS name',
            'r.mode AS mode',
        ];
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select($columns)
                     ->from(Recipe::class, 'r')
                     ->innerJoin('r.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->addOrderBy('r.name', 'ASC');

        /** @var array<array{id: UuidInterface, name: string, mode: string}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $this->mapRecipeDataResult($queryResult);
    }

    /**
     * Maps the query result to instances of RecipeData.
     * @param array<array{id: UuidInterface, name: string, mode: string, itemId?: string}> $recipeData
     * @return array<RecipeData>
     */
    protected function mapRecipeDataResult(array $recipeData): array
    {
        $result = [];
        foreach ($recipeData as $row) {
            $data = new RecipeData();
            $data->setId($row['id'])
                 ->setName($row['name'])
                 ->setMode($row['mode']);

            if (isset($row['itemId'])) {
                $data->setItemId(Uuid::fromBytes($row['itemId']));
            }

            $result[] = $data;
        }
        return $result;
    }

    /**
     * Orders the recipe data by their number of ingredients or products.
     * @param class-string<RecipeIngredient|RecipeProduct> $class
     * @param array<RecipeData> $recipeData
     * @return array<RecipeData>
     */
    protected function orderByNumberOfItems(string $class, array $recipeData): array
    {
        $recipeIds = array_map(fn (RecipeData $recipeData) => $recipeData->getId(), $recipeData);

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select(['IDENTITY(i.recipe) AS recipeId', 'MAX(i.order) AS number'])
                     ->from($class, 'i')
                     ->andWhere('i.recipe IN (:recipeIds)')
                     ->addGroupBy('i.recipe')
                     ->setParameter('recipeIds', $this->mapIdsToParameterValues($recipeIds));

        /** @var array<array{recipeId: string, number: string}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        $numbers = [];
        foreach ($queryResult as $row) {
            $numbers[$row['recipeId']] = intval($row['number']);
        }

        // Workaround: sort family is not stable as of PHP 7.4. Will be stable in PHP 8.0.
        $newData = [];
        foreach ($recipeData as $index => $data) {
            $sortKey = ($numbers[$data->getId()->getBytes()] ?? 0) * count($recipeData) + $index;
            $newData[$sortKey] = $data;
        }
        ksort($newData);
        return array_values($newData);
    }
}
