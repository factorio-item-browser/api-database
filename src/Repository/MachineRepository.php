<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\MachineData;
use FactorioItemBrowser\Api\Database\Entity\Machine;

/**
 * The repository class of the machine database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MachineRepository extends AbstractIdRepositoryWithOrphans
{
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
     * Finds the data of the machines with the specified names.
     * @param array|string[] $names
     * @param array|int[] $modCombinationIds
     * @return array|MachineData[]
     */
    public function findDataByNames(array $names, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($names) > 0) {
            $columns = [
                'm.id AS id',
                'm.name AS name',
                'mc.order AS order'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Machine::class, 'm')
                         ->innerJoin('m.modCombinations', 'mc')
                         ->andWhere('m.name IN (:names)')
                         ->setParameter('names', array_values($names));

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }
            $result = $this->mapMachineDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Finds the data of the machines supporting the specified crafting categories.
     * @param array|string[] $craftingCategories
     * @param array|int[] $modCombinationIds
     * @return array|MachineData[]
     */
    public function findDataByCraftingCategories(array $craftingCategories, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($craftingCategories) > 0) {
            $columns = [
                'm.id AS id',
                'm.name AS name',
                'mc.order AS order'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Machine::class, 'm')
                         ->innerJoin('m.craftingCategories', 'cc')
                         ->innerJoin('m.modCombinations', 'mc')
                         ->andWhere('cc.name IN (:craftingCategories)')
                         ->setParameter('craftingCategories', array_values($craftingCategories));

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }
            $result = $this->mapMachineDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Maps the query result to instances of MachineData.
     * @param array $machineData
     * @return array|MachineData[]
     */
    protected function mapMachineDataResult(array $machineData): array
    {
        $result = [];
        foreach ($machineData as $data) {
            $result[] = MachineData::createFromArray($data);
        }
        return $result;
    }
}
