<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\IconImage;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the icon image database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @extends AbstractIdRepositoryWithOrphans<IconImage>
 * @method array|IconImage[] findByIds(array|UuidInterface[] $ids)
 */
class IconImageRepository extends AbstractIdRepositoryWithOrphans
{
    protected function getEntityClass(): string
    {
        return IconImage::class;
    }

    /**
     * Adds the conditions to the query builder for detecting orphans.
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     */
    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.icons", 'i')
                     ->andWhere('i.image IS NULL');
    }
}
