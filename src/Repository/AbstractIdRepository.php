<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * The abstract class for repositories having an id as primary index.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractIdRepository
{
    /**
     * The entity manager.
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Initializes the repository.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns the entity class this repository manages.
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * Returns the entities with the specified ids.
     * @param array|UuidInterface[] $ids
     * @return array|object[]
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
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Maps the ids to their parameter values.
     * @param array|UuidInterface[] $ids
     * @return array|string[]
     */
    protected function mapIdsToParameterValues(array $ids): array
    {
        return array_map(function (UuidInterface $id): string {
            return $id->getBytes();
        }, $ids);
    }
}