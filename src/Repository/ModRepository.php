<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\Api\Database\Entity\Mod;

/**
 * The repository class of the Mod database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModRepository extends AbstractRepository
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
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select(['m', 'd', 'dm'])
                         ->from(Mod::class, 'm')
                         ->leftJoin('m.dependencies', 'd')
                         ->leftJoin('d.requiredMod', 'dm')
                         ->andWhere('m.name IN (:modNames)')
                         ->setParameter('modNames', array_values($modNames));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Returns all the mods.
     * @return array|Mod[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m')
                     ->from(Mod::class, 'm');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Counts the mods.
     * @param array|int[] $modCombinationIds
     * @return int
     */
    public function count(array $modCombinationIds = []): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('COUNT(DISTINCT m.id) AS numberOfMods')
                     ->from(Mod::class, 'm');

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
