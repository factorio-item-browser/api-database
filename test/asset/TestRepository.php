<?php

declare(strict_types=1);

namespace FactorioItemBrowserTestAsset\Api\Database;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindAllInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindAllTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use stdClass;

/**
 * The test repository implementing all the feature traits.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindAllInterface<stdClass>
 * @implements FindByIdsInterface<stdClass>
 * @implements FindByTypesAndNamesInterface<stdClass>
 */
class TestRepository implements
    FindAllInterface,
    FindByIdsInterface,
    FindByTypesAndNamesInterface,
    RemoveOrphansInterface
{
    /** @use FindAllTrait<stdClass> */
    use FindAllTrait;
    /** @use FindByIdsTrait<stdClass> */
    use FindByIdsTrait;
    /** @use FindByTypesAndNamesTrait<stdClass> */
    use FindByTypesAndNamesTrait;
    /** @use RemoveOrphansTrait<stdClass> */
    use RemoveOrphansTrait;

    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    protected function getEntityClass(): string
    {
        return stdClass::class;
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
    }
}
