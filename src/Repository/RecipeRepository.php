<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
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
 * @implements FindByIdsInterface<Recipe>
 */
class RecipeRepository implements
    FindByIdsInterface,
    RemoveOrphansInterface
{
    /** @use FindByIdsTrait<Recipe> */
    use FindByIdsTrait;
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

    /**
     * Finds the data of the recipes with the specified names.
     * @param array<string> $names
     * @return array<RecipeData>
     */
    public function findDataByNames(UuidInterface $combinationId, array $names): array
    {
        if (count($names) === 0) {
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
                     ->andWhere('r.name IN (:names)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('names', array_values($names));

        /** @var array<array{id: UuidInterface, name: string, mode: string}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $this->mapRecipeDataResult($queryResult);
    }

    /**
     * Finds the data of the recipes having the specified items as ingredients.
     * @param array<UuidInterface> $itemIds
     * @return array<RecipeData>
     */
    public function findDataByIngredientItemIds(UuidInterface $combinationId, array $itemIds): array
    {
        $data = $this->findDataByItemIds($combinationId, 'ingredients', $itemIds);
        return $this->orderByNumberOfItems(RecipeIngredient::class, $data);
    }

    /**
     * Finds the data of the recipes having the specified items as products.
     * @param array<UuidInterface> $itemIds
     * @return array<RecipeData>
     */
    public function findDataByProductItemIds(UuidInterface $combinationId, array $itemIds): array
    {
        $data = $this->findDataByItemIds($combinationId, 'products', $itemIds);
        return $this->orderByNumberOfItems(RecipeProduct::class, $data);
    }

    /**
     * Finds the data of recipes having a specific item involved.
     * @param array<UuidInterface> $itemIds
     * @return array<RecipeData>
     */
    protected function findDataByItemIds(UuidInterface $combinationId, string $recipeProperty, array $itemIds): array
    {
        if (count($itemIds) === 0) {
            return [];
        }

        $columns = [
            'r.id AS id',
            'r.name AS name',
            'r.mode AS mode',
            'IDENTITY(i.item) AS itemId',
        ];

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select($columns)
                     ->from(Recipe::class, 'r')
                     ->innerJoin('r.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->innerJoin("r.{$recipeProperty}", 'i', 'WITH', 'i.item IN (:itemIds)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('itemIds', $this->mapIdsToParameterValues($itemIds))
                     ->addOrderBy('r.name', 'ASC')
                     ->addOrderBy('r.mode', 'ASC');

        /** @var array<array{id: UuidInterface, name: string, mode: string, itemId: string}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $this->mapRecipeDataResult($queryResult);
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
