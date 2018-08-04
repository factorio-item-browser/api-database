<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityRepository;
use FactorioItemBrowser\Api\Database\Entity\ModCombination;

/**
 * The repository class of the ModCombination database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModCombinationRepository extends EntityRepository
{
    /**
     * Finds the combinations where the specified mod names are the main mod of.
     * @param array|string[] $modNames
     * @return array|ModCombination[]
     */
    public function findByModNames(array $modNames): array
    {
        $result = [];
        if (count($modNames) > 0) {
            $queryBuilder = $this->createQueryBuilder('mc');
            $queryBuilder->addSelect('m')
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
            $queryBuilder = $this->createQueryBuilder('mc');
            $queryBuilder->select('m.name')
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
}
