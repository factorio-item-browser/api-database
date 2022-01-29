<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Entity\TechnologyData;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;

/**
 * The repository for the TechnologyData entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<TechnologyData>
 */
class TechnologyDataRepository extends AbstractRepository implements
    FindByIdsInterface
{
    /** @use FindByIdsTrait<TechnologyData> */
    use FindByIdsTrait;

    protected function getEntityClass(): string
    {
        return TechnologyData::class;
    }
}
