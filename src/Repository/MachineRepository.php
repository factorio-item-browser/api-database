<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the machine database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<Machine>
 * @implements FindByNamesInterface<Machine>
 */
class MachineRepository implements
    FindByIdsInterface,
    FindByNamesInterface,
    RemoveOrphansInterface
{
    /** @use FindByIdsTrait<Machine> */
    use FindByIdsTrait;
    /** @use FindByNamesTrait<Machine> */
    use FindByNamesTrait;
    /** @use RemoveOrphansTrait<Machine> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return Machine::class;
    }

    protected function extendQueryForFindByIds(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->addSelect('cat')
                     ->leftJoin("{$alias}.categories", 'cat');
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    /**
     * @param array<UuidInterface> $ids
     */
    protected function removeIds(array $ids): void
    {
        // We have to clear the cross-table by reading the machines and clearing the collection first.
        foreach ($this->findByIds($ids) as $machine) {
            $machine->getCategories()->clear();
            $this->entityManager->remove($machine);
        }
        $this->entityManager->flush();
    }

    /**
     * Finds the machines supporting the provided category.
     * @param UuidInterface $categoryId
     * @param UuidInterface|null $combinationId
     * @return array<Machine>
     */
    public function findByCategory(UuidInterface $categoryId, ?UuidInterface $combinationId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m', 'cat')
                     ->from(Machine::class, 'm')
                     ->innerJoin('m.categories', 'cat', 'WITH', 'cat.id = :categoryId')
                     ->setParameter('categoryId', $categoryId, CustomTypes::UUID);

        if ($combinationId !== null) {
            $queryBuilder->innerJoin('m.combinations', 'c', 'WITH', 'c.id = :combinationId')
                         ->setParameter('combinationId', $combinationId, CustomTypes::UUID);
        }

        /** @var array<Machine> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
