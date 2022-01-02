<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the recipe database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @extends AbstractIdRepositoryWithOrphans<Recipe>
 */
class RecipeRepository extends AbstractIdRepositoryWithOrphans
{
    public function findByIds(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('r', 'ri', 'rii', 'rp', 'rpi')
                     ->from(Recipe::class, 'r')
                     ->leftJoin('r.ingredients', 'ri')
                     ->leftJoin('ri.item', 'rii')
                     ->leftJoin('r.products', 'rp')
                     ->leftJoin('rp.item', 'rpi')
                     ->andWhere('r.id IN (:ids)')
                     ->setParameter('ids', $this->mapIdsToParameterValues($ids));
        return $queryBuilder->getQuery()->getResult();
    }

    protected function getEntityClass(): string
    {
        return Recipe::class;
    }

    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    protected function removeIds(array $ids): void
    {
        $entityClasses = [
            RecipeIngredient::class => 'recipe',
            RecipeProduct::class => 'recipe',
            Recipe::class => 'id',
        ];

        foreach ($entityClasses as $entityClass => $property) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->delete($entityClass, 'e')
                         ->andWhere("e.{$property} IN (:ids)")
                         ->setParameter('ids', $this->mapIdsToParameterValues($ids));
            $queryBuilder->getQuery()->execute();
        }
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

        return $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
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

        return $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
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

        return $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
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

        return $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
    }

    /**
     * Maps the query result to instances of RecipeData.
     * @param array<mixed> $recipeData
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

        $numbers = [];
        foreach ($queryBuilder->getQuery()->getResult() as $row) {
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
