<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\UuidInterface;

/**
 * The trait implementing the findByTypesAndNames() method for the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 * @implements FindByTypesAndNamesInterface<TEntity>
 */
trait FindByTypesAndNamesTrait
{
    protected readonly EntityManagerInterface $entityManager;

    /**
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

    public function findByTypesAndNames(NamesByTypes $namesByTypes, ?UuidInterface $combinationId = null): array
    {
        if ($namesByTypes->isEmpty()) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from($this->getEntityClass(), 'e');

        if ($combinationId !== null) {
            $queryBuilder->innerJoin('e.combinations', 'c', 'WITH', 'c.id = :combinationId')
                         ->setParameter('combinationId', $combinationId, CustomTypes::UUID);
        }

        $index = 0;
        foreach ($namesByTypes->toArray() as $type => $names) {
            $queryBuilder->orWhere("e.type = :type{$index} AND e.name IN (:names{$index})")
                         ->setParameter("type{$index}", $type)
                         ->setParameter("names{$index}", array_values($names));
            ++$index;
        }

        $this->extendQueryForFindByTypesAndNames($queryBuilder, 'e');

        /** @var array<TEntity> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * Extends the findByTypesAndNames query with additional conditions.
     */
    protected function extendQueryForFindByTypesAndNames(QueryBuilder $queryBuilder, string $alias): void
    {
    }
}
