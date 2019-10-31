<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the machine database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MachineRepository extends AbstractIdRepositoryWithOrphans
{
    /**
     * Returns the entities with the specified ids.
     * @param array|UuidInterface[] $ids
     * @return array|Machine[]
     */
    public function findByIds(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m', 'cc')
                     ->from(Machine::class, 'm')
                     ->leftJoin('m.craftingCategories', 'cc')
                     ->andWhere('m.id IN (:ids)')
                     ->setParameter('ids', $this->mapIdsToParameterValues($ids));
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Returns the entity class this repository manages.
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Machine::class;
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
     * Removes the entities with the specified ids from the database.
     * @param array|UuidInterface[] $ids
     */
    protected function removeIds(array $ids): void
    {
        // We have to clear the cross-table by reading the machines and clearing the collection first.
        foreach ($this->findByIds($ids) as $machine) {
            $machine->getCraftingCategories()->clear();
            $this->entityManager->remove($machine);
        }
        $this->entityManager->flush();
    }

    /**
     * Finds the data of the machines with the specified names.
     * @param UuidInterface $combinationId
     * @param array|string[] $names
     * @return array|Machine[]
     */
    public function findByNames(UuidInterface $combinationId, array $names): array
    {
        if (count($names) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m')
                     ->from(Machine::class, 'm')
                     ->innerJoin('m.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->andWhere('m.name IN (:names)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('names', array_values($names));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Finds the machines supporting the specified crafting categories.
     * @param UuidInterface $combinationId
     * @param string $craftingCategoryName
     * @return array|Machine[]
     */
    public function findByCraftingCategoryName(UuidInterface $combinationId, string $craftingCategoryName): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m')
                     ->from(Machine::class, 'm')
                     ->innerJoin('m.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->innerJoin('m.craftingCategories', 'cc', 'WITH', 'cc.name = :craftingCategoryName')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('craftingCategoryName', $craftingCategoryName);

        return $queryBuilder->getQuery()->getResult();
    }
}
