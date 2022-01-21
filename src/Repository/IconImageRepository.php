<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\IconData;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the icon image database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @extends AbstractIdRepositoryWithOrphans<IconData>
 * @method IconData[] findByIds(UuidInterface[] $ids)
 */
class IconImageRepository extends AbstractIdRepositoryWithOrphans
{
    protected function getEntityClass(): string
    {
        return IconData::class;
    }

    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.icons", 'i')
                     ->andWhere('i.image IS NULL');
    }
}
