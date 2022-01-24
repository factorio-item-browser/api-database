<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository\Feature;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\UuidInterface;

/**
 * The trait implementing the findAll() method for the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template TEntity
 * @implements FindAllInterface<TEntity>
 */
trait FindAllTrait
{
    protected readonly EntityManagerInterface $entityManager;

    /**
     * @return class-string<TEntity>
     */
    abstract protected function getEntityClass(): string;

    public function findAll(UuidInterface $combinationId, int $numberOfResults, int $indexOfFirstResult): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('e')
                     ->from($this->getEntityClass(), 'e')
                     ->innerJoin('e.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, CustomTypes::UUID)
                     ->setMaxResults($numberOfResults)
                     ->setFirstResult($indexOfFirstResult);
        $this->extendQueryForFindAll($queryBuilder, 'e');

        /** @var array<TEntity> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * Extends the findAll query with additional conditions.
     */
    protected function extendQueryForFindAll(QueryBuilder $queryBuilder, string $alias): void
    {
    }
}
