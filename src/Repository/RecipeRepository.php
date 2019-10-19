<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\Recipe;

/**
 * The repository class of the recipe database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeRepository extends AbstractIdRepositoryWithOrphans
{
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
     * Finds the data of the recipes with the specified names.
     * @param array|string[] $names
     * @param array|int[] $modCombinationIds
     * @return array|RecipeData[]
     */
    public function findDataByNames(array $names, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($names) > 0) {
            $columns = [
                'r.id AS id',
                'r.name AS name',
                'r.mode AS mode',
                'mc.order AS order'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Recipe::class, 'r')
                         ->innerJoin('r.modCombinations', 'mc')
                         ->andWhere('r.name IN (:names)')
                         ->setParameter('names', array_values($names));

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Finds the data of the recipes having the specified items as ingredients.
     * @param array|int[] $itemIds
     * @param array|int[] $modCombinationIds
     * @return array|RecipeData[]
     */
    public function findDataByIngredientItemIds(array $itemIds, array $modCombinationIds = []): array
    {
        return $this->findDataByItemIds('ingredients', $itemIds, $modCombinationIds);
    }

    /**
     * Finds the data of the recipes having the specified items as products.
     * @param array|int[] $itemIds
     * @param array|int[] $modCombinationIds
     * @return array|RecipeData[]
     */
    public function findDataByProductItemIds(array $itemIds, array $modCombinationIds = []): array
    {
        return $this->findDataByItemIds('products', $itemIds, $modCombinationIds);
    }

    /**
     * Finds the data of recipes having a specific item involved.
     * @param string $recipeProperty
     * @param array|int[] $itemIds
     * @param array|int[] $modCombinationIds
     * @return array|RecipeData[]
     */
    protected function findDataByItemIds(string $recipeProperty, array $itemIds, array $modCombinationIds): array
    {
        $result = [];
        if (count($itemIds) > 0) {
            $columns = [
                'r.id AS id',
                'r.name AS name',
                'r.mode AS mode',
                'IDENTITY(r2.item) AS itemId',
                'mc.order AS order'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Recipe::class, 'r')
                         ->innerJoin('r.' . $recipeProperty, 'r2')
                         ->innerJoin('r.modCombinations', 'mc')
                         ->andWhere('r2.item IN (:itemIds)')
                         ->setParameter('itemIds', array_values($itemIds))
                         ->addOrderBy('r.name', 'ASC')
                         ->addOrderBy('r.mode', 'ASC');

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Finds the data of the recipes with the specified keywords.
     * @param array|string[] $keywords
     * @param array|int[] $modCombinationIds
     * @return array|RecipeData[]
     */
    public function findDataByKeywords(array $keywords, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($keywords) > 0) {
            $columns = [
                'r.id AS id',
                'r.name AS name',
                'r.mode AS mode',
                'mc.order AS order'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Recipe::class, 'r')
                         ->innerJoin('r.modCombinations', 'mc');

            $index = 0;
            foreach ($keywords as $keyword) {
                $queryBuilder->andWhere('r.name LIKE :keyword' . $index)
                             ->setParameter('keyword' . $index, '%' . addcslashes($keyword, '\\%_') . '%');
                ++$index;
            }

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $this->mapRecipeDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Maps the query result to instances of RecipeData.
     * @param array $recipeData
     * @return array|RecipeData[]
     */
    protected function mapRecipeDataResult(array $recipeData): array
    {
        $result = [];
        foreach ($recipeData as $data) {
            $result[] = RecipeData::createFromArray($data);
        }
        return $result;
    }
    
//    /**
//     * Finds the recipes of the specified IDs, including ingredient and product data.
//     * @param array|int[] $ids
//     * @return array|Recipe[]
//     */
//    public function findByIds(array $ids): array
//    {
//        $result = [];
//        if (count($ids) > 0) {
//            $queryBuilder = $this->entityManager->createQueryBuilder();
//            $queryBuilder->select(['r', 'ri', 'rii', 'rp', 'rpi'])
//                         ->from(Recipe::class, 'r')
//                         ->leftJoin('r.ingredients', 'ri')
//                         ->leftJoin('ri.item', 'rii')
//                         ->leftJoin('r.products', 'rp')
//                         ->leftJoin('rp.item', 'rpi')
//                         ->andWhere('r.id IN (:ids)')
//                         ->setParameter('ids', array_values($ids));
//
//            $result = $queryBuilder->getQuery()->getResult();
//        }
//        return $result;
//    }
}
