<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\NonUniqueResultException;
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
}
