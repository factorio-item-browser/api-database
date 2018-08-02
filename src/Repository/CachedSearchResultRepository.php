<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;

/**
 * The repository class of the cached search result database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResultRepository extends EntityRepository
{
    /**
     * Finds the search results with the specified hashes.
     * @param array|string[] $hashes
     * @param DateTime $maxAge
     * @return array|CachedSearchResult[]
     */
    public function findByHashes(array $hashes, DateTime $maxAge): array
    {
        $result = [];
        if (count($hashes) > 0) {
            $queryBuilder = $this->createQueryBuilder('r');
            $queryBuilder->andWhere('r.hash IN (:hashes)')
                         ->andWhere('r.lastSearchTime > :maxAge')
                         ->setParameter('hashes', array_map('hex2bin', $hashes))
                         ->setParameter('maxAge', $maxAge);

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Cleans up no longer needed data.
     * @param DateTime $maxAge
     * @return $this
     */
    public function cleanup(DateTime $maxAge)
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->delete($this->getEntityName(), 'r')
                     ->andWhere('r.lastSearchTime < :maxAge')
                     ->setParameter('maxAge', $maxAge);

        $queryBuilder->getQuery()->execute();
        return $this;
    }

    /**
     * Clears the database table, emptying the cache.
     * @return $this
     */
    public function clear()
    {
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->delete($this->getEntityName(), 'r');

        $queryBuilder->getQuery()->execute();
        return $this;
    }
}
