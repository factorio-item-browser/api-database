<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the Mod database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @extends AbstractIdRepositoryWithOrphans<Mod>
 * @method Mod[] findByIds(UuidInterface[] $ids)
 */
class ModRepository extends AbstractIdRepositoryWithOrphans
{
    protected function getEntityClass(): string
    {
        return Mod::class;
    }

    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    /**
     * Returns all mods used by the specified combination.
     * @return array<Mod>
     */
    public function findByCombinationId(UuidInterface $combinationId): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m')
                     ->from(Mod::class, 'm')
                     ->innerJoin('m.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->orderBy('m.name', 'ASC');

        /** @var array<Mod> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
