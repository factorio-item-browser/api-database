<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Entity\IconData;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository for the IconData entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<IconData>
 */
class IconDataRepository implements
    FindByIdsInterface,
    RemoveOrphansInterface
{
    /** @use FindByIdsTrait<IconData> */
    use FindByIdsTrait;
    /** @use RemoveOrphansTrait<IconData> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return IconData::class;
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.icons", 'i')
                     ->andWhere('i.id IS NULL');
    }

    /**
     * Finds the entities used by icons with the provided types and names.
     * @return array<IconData>
     */
    public function findByIconTypesAndNames(NamesByTypes $namesByTypes, ?UuidInterface $combinationId = null): array
    {
        if ($namesByTypes->isEmpty()) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('id')
                     ->from(IconData::class, 'id')
                     ->innerJoin(Icon::class, 'i', 'WITH', 'i.data = id');

        if ($combinationId !== null) {
            $queryBuilder->innerJoin('i.combinations', 'c', 'WITH', 'c.id = :combinationId')
                         ->setParameter('combinationId', $combinationId, CustomTypes::UUID);
        }

        $index = 0;
        foreach ($namesByTypes->toArray() as $type => $names) {
            $queryBuilder->orWhere("i.type = :type{$index} AND i.name IN (:names{$index})")
                         ->setParameter("type{$index}", $type)
                         ->setParameter("names{$index}", array_values($names));
            ++$index;
        }

        /** @var array<IconData> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }
}
