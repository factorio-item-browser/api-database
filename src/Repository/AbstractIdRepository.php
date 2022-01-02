<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Ramsey\Uuid\UuidInterface;

/**
 * The abstract class for repositories having an id as primary index.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 */
abstract class AbstractIdRepository extends AbstractRepository
{
    /**
     * Returns the entity class this repository manages.
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

    /**
     * Returns the entities with the specified ids.
     * @param array<UuidInterface> $ids
     * @return array<TEntity>
     */
    public function findByIds(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from($this->getEntityClass(), 'e')
                     ->andWhere('e.id IN (:ids)')
                     ->setParameter('ids', $this->mapIdsToParameterValues($ids));

        /** @var array<TEntity> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
