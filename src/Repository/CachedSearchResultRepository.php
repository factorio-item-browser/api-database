<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use DateTime;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;

/**
 * The repository class of the cached search result database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResultRepository extends AbstractRepository
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
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('r')
                         ->from(CachedSearchResult::class, 'r')
                         ->andWhere('r.hash IN (:hashes)')
                         ->andWhere('r.lastSearchTime > :maxAge')
                         ->setParameter('hashes', array_values(array_map('hex2bin', $hashes)))
                         ->setParameter('maxAge', $maxAge);

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Persists the specified cached search result into the database.
     * @param CachedSearchResult $cachedSearchResult
     */
    public function persist(CachedSearchResult $cachedSearchResult): void
    {
        $cachedSearchResult = $this->entityManager->merge($cachedSearchResult);
        $this->entityManager->persist($cachedSearchResult);
        $this->entityManager->flush();
    }

    /**
     * Cleans up no longer needed data.
     * @param DateTime $maxAge
     */
    public function cleanup(DateTime $maxAge): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CachedSearchResult::class, 'r')
                     ->andWhere('r.lastSearchTime < :maxAge')
                     ->setParameter('maxAge', $maxAge);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * Clears the database table, emptying the cache.
     */
    public function clear(): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CachedSearchResult::class, 'r');

        $queryBuilder->getQuery()->execute();
    }
}
