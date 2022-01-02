<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * The abstract repository with possible orphans to be removed.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 * @extends AbstractIdRepository<TEntity>
 */
abstract class AbstractIdRepositoryWithOrphans extends AbstractIdRepository
{
    /**
     * Removes any orphaned entities.
     */
    public function removeOrphans(): void
    {
        $ids = $this->findOrphanedIds();
        foreach (array_chunk($ids, 1024) as $chunkedIds) {
            $this->removeIds($chunkedIds);
        }
    }

    /**
     * Returns the ids of orphaned entities.
     * @return array<UuidInterface>
     */
    protected function findOrphanedIds(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e.id AS id')
                     ->from($this->getEntityClass(), 'e');
        $this->addOrphanConditions($queryBuilder, 'e');

        /** @var array<array{id: UuidInterface}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        $result = [];
        foreach ($queryResult as $data) {
            $result[] = $data['id'];
        }
        return $result;
    }

    /**
     * Adds the conditions to the query builder for detecting orphans.
     */
    abstract protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void;

    /**
     * Removes the entities with the specified ids from the database.
     * @param array<UuidInterface> $ids
     */
    protected function removeIds(array $ids): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete($this->getEntityClass(), 'e')
                     ->andWhere('e.id IN (:ids)')
                     ->setParameter('ids', $this->mapIdsToParameterValues($ids));
        $queryBuilder->getQuery()->execute();
    }
}
