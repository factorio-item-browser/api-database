<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\UuidInterface;

/**
 * The trait implementing the findByNames() method for the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 * @implements FindByNamesInterface<TEntity>
 */
trait FindByNamesTrait
{
    protected readonly EntityManagerInterface $entityManager;

    /**
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

    public function findByNames(array $names, ?UuidInterface $combinationId = null): array
    {
        if (count($names) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from($this->getEntityClass(), 'e')
                     ->andWhere('e.name IN (:names)')
                     ->setParameter('names', array_values($names));

        if ($combinationId !== null) {
            $queryBuilder->innerJoin('e.combinations', 'c', 'WITH', 'c.id = :combinationId')
                         ->setParameter('combinationId', $combinationId, CustomTypes::UUID);
        }

        $this->extendQueryForFindByNames($queryBuilder, 'e');

        /** @var array<TEntity> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * Extends the findByNames query with additional conditions.
     */
    protected function extendQueryForFindByNames(QueryBuilder $queryBuilder, string $alias): void
    {
    }
}
