<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Entity\Category;

/**
 * The repository class of the crafting category database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @extends AbstractIdRepositoryWithOrphans<Category>
 */
class CategoryRepository extends AbstractIdRepositoryWithOrphans
{
    protected function getEntityClass(): string
    {
        return Category::class;
    }

    /**
     * Finds the categories with the specified types and names.
     * @return array<Category>
     */
    public function findByTypesAndNames(NamesByTypes $namesByTypes): array
    {
        if ($namesByTypes->isEmpty()) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c')
                     ->from(Category::class, 'c');

        $index = 0;
        foreach ($namesByTypes->toArray() as $type => $names) {
            $queryBuilder->orWhere("c.type = :type{$index} AND c.name IN (:names{$index})")
                         ->setParameter("type{$index}", $type)
                         ->setParameter("names{$index}", array_values($names));
            ++$index;
        }

        /** @var array<Category> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.machines", 'm')
                     ->leftJoin("{$alias}.recipes", 'r')
                     ->andWhere('m.id IS NULL')
                     ->andWhere('r.id IS NULL');
    }
}
