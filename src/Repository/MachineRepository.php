<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityRepository;
use FactorioItemBrowser\Api\Database\Data\MachineData;
use FactorioItemBrowser\Api\Database\Entity\Machine;

/**
 * The repository class of the machine database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MachineRepository extends EntityRepository
{
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

            $queryBuilder = $this->createQueryBuilder('m');
            $queryBuilder->select($columns)
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

            $queryBuilder = $this->createQueryBuilder('m');
            $queryBuilder->select($columns)
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

    /**
     * Finds the machines of the specified IDs, including all details.
     * @param array|int[] $ids
     * @return array|Machine[]
     */
    public function findByIds(array $ids): array
    {
        $result = [];
        if (count($ids) > 0) {
            $queryBuilder = $this->createQueryBuilder('m');
            $queryBuilder->addSelect('cc')
                         ->leftJoin('m.craftingCategories', 'cc')
                         ->andWhere('m.id IN (:ids)')
                         ->setParameter('ids', array_values($ids));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Removes any orphaned machines, i.e. machines no longer used by any combination.
     * @return $this
     */
    public function removeOrphans()
    {
        $machineIds = $this->findOrphanedIds();
        if (count($machineIds) > 0) {
            $this->removeIds($machineIds);
        }
        return $this;
    }

    /**
     * Returns the ids of orphaned machines, which are no longer used by any combination.
     * @return array|int[]
     */
    protected function findOrphanedIds(): array
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->select('m.id AS id')
                     ->leftJoin('m.modCombinations', 'mc')
                     ->andWhere('mc.id IS NULL');

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $data) {
            $result[] = (int) $data['id'];
        }
        return $result;
    }

    /**
     * Removes the machines with the specified ids from the database.
     * @param array|int[] $machineIds
     * @return $this
     */
    protected function removeIds(array $machineIds)
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->delete($this->getEntityName(), 'm')
                     ->andWhere('m.id IN (:machineIds)')
                     ->setParameter('machineIds', array_values($machineIds));
        $queryBuilder->getQuery()->execute();
        return $this;
    }
}
