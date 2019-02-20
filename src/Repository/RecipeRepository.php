<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;

/**
 * The repository class of the recipe database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeRepository extends AbstractRepository implements RepositoryWithOrphansInterface
{
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
    
    /**
     * Finds the recipes of the specified IDs, including ingredient and product data.
     * @param array|int[] $ids
     * @return array|Recipe[]
     */
    public function findByIds(array $ids): array
    {
        $result = [];
        if (count($ids) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select(['r', 'ri', 'rii', 'rp', 'rpi'])
                         ->from(Recipe::class, 'r')
                         ->leftJoin('r.ingredients', 'ri')
                         ->leftJoin('ri.item', 'rii')
                         ->leftJoin('r.products', 'rp')
                         ->leftJoin('rp.item', 'rpi')
                         ->andWhere('r.id IN (:ids)')
                         ->setParameter('ids', array_values($ids));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Removes any orphaned recipes, i.e. recipes no longer used by any combination.
     */
    public function removeOrphans(): void
    {
        $recipeIds = $this->findOrphanedIds();
        if (count($recipeIds) > 0) {
            $this->removeIds($recipeIds);
        }
    }

    /**
     * Returns the ids of orphaned machines, which are no longer used by any combination.
     * @return array|int[]
     */
    protected function findOrphanedIds(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('r.id AS id')
                     ->from(Recipe::class, 'r')
                     ->leftJoin('r.modCombinations', 'mc')
                     ->andWhere('mc.id IS NULL');

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $data) {
            $result[] = (int) $data['id'];
        }
        return $result;
    }

    /**
     * Removes the recipes with the specified ids from the database.
     * @param array|int[] $recipeIds
     */
    protected function removeIds(array $recipeIds): void
    {
        // First delete the ingredients...
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(RecipeIngredient::class, 'ri')
                     ->andWhere('ri.recipe IN (:recipeIds)')
                     ->setParameter('recipeIds', array_values($recipeIds));
        $queryBuilder->getQuery()->execute();

        // ... and the products.
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(RecipeProduct::class, 'rp')
                     ->andWhere('rp.recipe IN (:recipeIds)')
                     ->setParameter('recipeIds', array_values($recipeIds));
        $queryBuilder->getQuery()->execute();

        // And finally the recipes itself.
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(Recipe::class, 'r')
                     ->andWhere('r.id IN (:recipeIds)')
                     ->setParameter('recipeIds', array_values($recipeIds));

        $queryBuilder->getQuery()->execute();
    }
}
