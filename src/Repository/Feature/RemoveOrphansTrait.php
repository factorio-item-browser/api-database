<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * The trait implementing the removeOrphans() method for the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 */
trait RemoveOrphansTrait
{
    protected readonly EntityManagerInterface $entityManager;

    /**
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

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
        $this->addRemoveOrphansConditions($queryBuilder, 'e');

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
    abstract protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void;

    /**
     * Removes the entities with the specified ids from the database.
     * @param array<UuidInterface> $ids
     */
    protected function removeIds(array $ids): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete($this->getEntityClass(), 'e')
                     ->andWhere('e.id IN (:ids)')
                     ->setParameter('ids', array_map(fn(UuidInterface $id): string => $id->getBytes(), $ids));
        $queryBuilder->getQuery()->execute();
    }
}
