<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the icon database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<Icon>
 * @implements FindByTypesAndNamesInterface<Icon>
 */
class IconRepository implements
    FindByIdsInterface,
    FindByTypesAndNamesInterface,
    RemoveOrphansInterface
{
    /** @use FindByIdsTrait<Icon> */
    use FindByIdsTrait;
    /** @use FindByTypesAndNamesTrait<Icon> */
    use FindByTypesAndNamesTrait;
    /** @use RemoveOrphansTrait<Icon> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return Icon::class;
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    /**
     * Finds the icons using one of the provided data ids.
     * @param array<UuidInterface> $dataIds
     * @return array<Icon>
     */
    public function findByDataIds(array $dataIds, ?UuidInterface $combinationId = null): array
    {
        if (count($dataIds) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Icon::class, 'i')
                     ->andWhere('i.data IN (:dataIds)')
                     ->setParameter('dataIds', array_map(fn(UuidInterface $id): string => $id->getBytes(), $dataIds));

        if ($combinationId !== null) {
            $queryBuilder->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                         ->setParameter('combinationId', $combinationId, CustomTypes::UUID);
        }

        /** @var array<Icon> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
