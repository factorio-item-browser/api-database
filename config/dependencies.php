<?php

declare(strict_types=1);

/**
 * The configuration of the database dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Database;

use ContainerInteropDoctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;

return [
    'dependencies' => [
        'factories'  => [
            Repository\CachedSearchResultRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\CraftingCategoryRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\IconFileRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\IconRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\ItemRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\MachineRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\ModRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\ModCombinationRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\RecipeRepository::class => Repository\AbstractRepositoryFactory::class,
            Repository\TranslationRepository::class => Repository\AbstractRepositoryFactory::class,

            // 3rd-party dependencies
            EntityManagerInterface::class => EntityManagerFactory::class,
        ],
    ],
];
