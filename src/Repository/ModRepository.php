<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\Api\Database\Entity\Mod;

/**
 * The repository class of the Mod database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModRepository extends EntityRepository
{
    /**
     * Finds all mods with the specified names, fetching their dependencies as well.
     * @param array|string[] $modNames
     * @return array|Mod[]
     */
    public function findByNamesWithDependencies(array $modNames): array
    {
        $result = [];
        if (count($modNames) > 0) {
            $queryBuilder = $this->createQueryBuilder('m');
            $queryBuilder->addSelect('d')
                         ->addSelect('dm')
                         ->leftJoin('m.dependencies', 'd')
                         ->leftJoin('d.requiredMod', 'dm')
                         ->andWhere('m.name IN (:modNames)')
                         ->setParameter('modNames', array_values($modNames));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Counts the mods.
     * @param array|int[] $modCombinationIds
     * @return int
     */
    public function count(array $modCombinationIds = []): int
    {
        $queryBuilder = $this->createQueryBuilder('m');
        $queryBuilder->select('COUNT(DISTINCT m.id) AS numberOfMods');

        if (count($modCombinationIds) > 0) {
            $queryBuilder->innerJoin('m.combinations', 'mc')
                         ->andWhere('mc.id IN (:modCombinationIds)')
                         ->setParameter('modCombinationIds', array_values($modCombinationIds));
        }

        try {
            $result = (int) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            $result = 0;
        }
        return $result;
    }
}
