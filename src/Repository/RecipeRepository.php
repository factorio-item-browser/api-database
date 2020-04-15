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
 */
class RecipeRepository extends AbstractIdRepositoryWithOrphans
{
    /**
     * Returns the entities with the specified ids.
     * @param array|UuidInterface[] $ids
     * @return array|Recipe[]
     */
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

    /**
     * Returns the entity class this repository manages.
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Recipe::class;
    }

    /**
     * Adds the conditions to the query builder for detecting orphans.
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     */
    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    /**
     * Removes the entities with the specified ids from the database.
     * @param array|UuidInterface[] $ids
     */
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
     * @param UuidInterface $combinationId
     * @param array|string[] $names
     * @return array|RecipeData[]
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
     * @param UuidInterface $combinationId
     * @param array|UuidInterface[] $itemIds
     * @return array|RecipeData[]
     */
    public function findDataByIngredientItemIds(UuidInterface $combinationId, array $itemIds): array
    {
        return $this->findDataByItemIds($combinationId, 'ingredients', $itemIds);
    }

    /**
     * Finds the data of the recipes having the specified items as products.
     * @param UuidInterface $combinationId
     * @param array|UuidInterface[] $itemIds
     * @return array|RecipeData[]
     */
    public function findDataByProductItemIds(UuidInterface $combinationId, array $itemIds): array
    {
        return $this->findDataByItemIds($combinationId, 'products', $itemIds);
    }

    /**
     * Finds the data of recipes having a specific item involved.
     * @param UuidInterface $combinationId
     * @param string $recipeProperty
     * @param array|UuidInterface[] $itemIds
     * @return array|RecipeData[]
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
     * @param UuidInterface $combinationId
     * @param array|string[] $keywords
     * @return array|RecipeData[]
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
     * Maps the query result to instances of RecipeData.
     * @param array<mixed> $recipeData
     * @return array|RecipeData[]
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
}
