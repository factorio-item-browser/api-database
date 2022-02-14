<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
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
        $queryBuilder->addSelect('c')
                     ->leftJoin("{$alias}.categories", 'c');
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
     * Finds the machines supporting the specified crafting categories.
     * @return array<Machine>
     */
    public function findByCraftingCategoryName(UuidInterface $combinationId, string $craftingCategoryName): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('m')
                     ->from(Machine::class, 'm')
                     ->innerJoin('m.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->innerJoin('m.craftingCategories', 'cc', 'WITH', 'cc.name = :craftingCategoryName')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('craftingCategoryName', $craftingCategoryName);

        /** @var array<Machine> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
