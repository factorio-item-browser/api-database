<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the combination database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CombinationRepository extends AbstractRepository
{
    /**
     * Finds the combination with the specified id.
     * @param UuidInterface $id
     * @return Combination|null
     */
    public function findById(UuidInterface $id): ?Combination
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c')
                     ->from(Combination::class, 'c')
                     ->andWhere('c.id = :id')
                     ->setParameter('id', $id, UuidBinaryType::NAME);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // Will never happen, we are searching for the primary key.
            return null;
        }
    }

    /**
     * Finds combinations which may be possible to be updated.
     * @param DateTimeInterface $earliestUsageTime
     * @param DateTimeInterface $latestUpdateCheckTime
     * @param int $limit
     * @return array<Combination>
     */
    public function findPossibleCombinationsForUpdate(
        DateTimeInterface $earliestUsageTime,
        DateTimeInterface $latestUpdateCheckTime,
        int $limit
    ): array {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c')
                     ->from(Combination::class, 'c')
                     ->andWhere('c.lastUsageTime >= :lastUsageTime')
                     ->andWhere('(c.lastUpdateCheckTime IS NULL OR c.lastUpdateCheckTime < :lastUpdateCheckTime)')
                     ->andWhere('c.lastUsageTime > c.importTime')
                     ->addOrderBy('c.lastUsageTime', 'DESC')
                     ->setParameter('lastUsageTime', $earliestUsageTime)
                     ->setParameter('lastUpdateCheckTime', $latestUpdateCheckTime)
                     ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Updates the last usage time of the specified combination.
     * @param Combination $combination
     */
    public function updateLastUsageTime(Combination $combination): void
    {
        try {
            $combination->setLastUsageTime(new DateTime());
            $this->entityManager->persist($combination);
            $this->entityManager->flush();
        } catch (Exception $e) {
            // Nothing to do.
        }
    }
}
