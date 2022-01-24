<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * The trait implementing the findByIds() method for the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 * @implements FindByIdsInterface<TEntity>
 */
trait FindByIdsTrait
{
    protected readonly EntityManagerInterface $entityManager;

    /**
     * Returns the entity class this repository manages.
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

    public function findByIds(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from($this->getEntityClass(), 'e')
                     ->andWhere('e.id IN (:ids)')
                     ->setParameter('ids', array_map(fn(UuidInterface $id): string => $id->getBytes(), $ids));
        $this->extendQueryForFindByIds($queryBuilder, 'e');

        /** @var array<TEntity> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * Extends the findByIds query with additional conditions.
     */
    protected function extendQueryForFindByIds(QueryBuilder $queryBuilder, string $alias): void
    {
    }
}
