<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Entity\ModCombination;

/**
 * The repository class of the ModCombination database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModCombinationRepository extends AbstractRepository
{
    /**
     * Finds the combinations with the specified names.
     * @param array|string[] $names
     * @return array|ModCombination[]
     */
    public function findByNames(array $names): array
    {
        $result = [];
        if (count($names) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('mc')
                         ->from(ModCombination::class, 'mc')
                         ->andWhere('mc.name IN (:names)')
                         ->addOrderBy('mc.order', 'ASC')
                         ->setParameter('names', array_values($names));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Finds the combinations where the specified mod names are the main mod of.
     * @param array|string[] $modNames
     * @return array|ModCombination[]
     */
    public function findByModNames(array $modNames): array
    {
        $result = [];
        if (count($modNames) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select(['mc', 'm'])
                         ->from(ModCombination::class, 'mc')
                         ->innerJoin('mc.mod', 'm')
                         ->andWhere('m.name IN (:modNames)')
                         ->addOrderBy('mc.order', 'ASC')
                         ->setParameter('modNames', array_values($modNames));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Finds the mod names of the specified combination ids.
     * @param array|int[] $modCombinationIds
     * @return array|string[]
     */
    public function findModNamesByIds(array $modCombinationIds): array
    {
        $result = [];
        if (count($modCombinationIds) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('m.name')
                         ->from(ModCombination::class, 'mc')
                         ->innerJoin('mc.mod', 'm')
                         ->andWhere('mc.id IN (:modCombinationIds)')
                         ->addGroupBy('m.name')
                         ->setParameter('modCombinationIds', array_values($modCombinationIds));

            foreach ($queryBuilder->getQuery()->getResult() as $row) {
                $result[] = $row['name'];
            }
        }
        return $result;
    }

    /**
     * Returns all the mods.
     * @return array|ModCombination[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('mc')
                     ->from(ModCombination::class, 'mc');

        return $queryBuilder->getQuery()->getResult();
    }
}
