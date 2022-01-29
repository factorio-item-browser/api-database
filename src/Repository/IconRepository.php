<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
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
     * Finds the icons using one of the image ids.
     * @param array<UuidInterface> $imageIds
     * @return array<Icon>
     */
    public function findByImageIds(UuidInterface $combinationId, array $imageIds): array
    {
        if (count($imageIds) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Icon::class, 'i')
                     ->innerJoin('i.combination', 'c', 'WITH', 'c.id = :combinationId')
                     ->andWhere('i.image IN (:imageIds)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('imageIds', $this->mapIdsToParameterValues($imageIds));

        /** @var array<Icon> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

}
