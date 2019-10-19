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
 */
abstract class AbstractIdRepositoryWithOrphans extends AbstractIdRepository
{
    /**
     * Removes any orphaned entities.
     */
    public function removeOrphans(): void
    {
        $itemIds = $this->findOrphanedIds();
        if (count($itemIds) > 0) {
            $this->removeIds($itemIds);
        }
    }

    /**
     * Returns the ids of orphaned entities.
     * @return array|UuidInterface[]
     */
    protected function findOrphanedIds(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e.id AS id')
                     ->from($this->getEntityClass(), 'e');
        $this->addOrphanConditions($queryBuilder, 'e');

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $data) {
            $result[] = $data['id'];
        }
        return $result;
    }

    /**
     * Adds the conditions to the query builder for detecting orphans.
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     */
    abstract protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void;

    /**
     * Removes the entities with the specified ids from the database.
     * @param array|UuidInterface[] $ids
     */
    protected function removeIds(array $ids): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete($this->getEntityClass(), 'i')
                     ->andWhere('i.id IN (:ids)')
                     ->setParameter('ids', $this->mapIdsToParameterValues($ids));
        $queryBuilder->getQuery()->execute();
    }
}
