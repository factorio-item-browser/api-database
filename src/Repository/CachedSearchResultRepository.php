<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use DateTimeInterface;
use Doctrine\ORM\NonUniqueResultException;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the cached search result database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResultRepository extends AbstractRepository
{
    /**
     * Finds the cached search result. The returned may already be expired though.
     * @param UuidInterface $combinationId
     * @param string $locale
     * @param UuidInterface $searchHash
     * @return CachedSearchResult|null
     */
    public function find(UuidInterface $combinationId, string $locale, UuidInterface $searchHash): ?CachedSearchResult
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('csr')
                     ->from(CachedSearchResult::class, 'csr')
                     ->andWhere('csr.combinationId = :combinationId')
                     ->andWhere('csr.locale = :locale')
                     ->andWhere('csr.searchHash = :searchHash')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('locale', $locale)
                     ->setParameter('searchHash', $searchHash, UuidBinaryType::NAME);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // Can never happen, we are searching for the primary keys.
            return null;
        }
    }

    /**
     * Persists the specified cached search result into the database.
     * @param CachedSearchResult $cachedSearchResult
     */
    public function persist(CachedSearchResult $cachedSearchResult): void
    {
        $persistedEntity = $this->find(
            $cachedSearchResult->getCombinationId(),
            $cachedSearchResult->getLocale(),
            $cachedSearchResult->getSearchHash()
        );

        if ($persistedEntity instanceof CachedSearchResult) {
            $persistedEntity->setLastSearchTime($cachedSearchResult->getLastSearchTime());
        } else {
            $persistedEntity = $cachedSearchResult;
            $this->entityManager->persist($persistedEntity);
        }
        $this->entityManager->flush();
    }

    /**
     * Clears already expired search results from the database.
     * @param DateTimeInterface $maxAge
     */
    public function clearExpiredResults(DateTimeInterface $maxAge): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CachedSearchResult::class, 'csr')
                     ->andWhere('csr.lastSearchTime < :maxAge')
                     ->setParameter('maxAge', $maxAge);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * Clears all search results of the specified combination, e.g. because it just got updated.
     * @param UuidInterface $combinationId
     */
    public function clearResultsOfCombination(UuidInterface $combinationId): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CachedSearchResult::class, 'csr')
                     ->andWhere('csr.combinationId = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * Clears ALL search results from the database.
     */
    public function clearAll(): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->delete(CachedSearchResult::class, 'csr');

        $queryBuilder->getQuery()->execute();
    }
}
