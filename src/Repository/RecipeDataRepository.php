<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;

/**
 * The repository for the RecipeData entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<RecipeData>
 */
class RecipeDataRepository extends AbstractRepository implements
    FindByIdsInterface
{
    /** @use FindByIdsTrait<RecipeData> */
    use FindByIdsTrait;

    protected function getEntityClass(): string
    {
        return RecipeData::class;
    }
}
