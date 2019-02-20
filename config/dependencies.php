<?php

declare(strict_types=1);

/**
 * The configuration of the database dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Database;

use Blast\ReflectionFactory\ReflectionFactory;
use ContainerInteropDoctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'factories'  => [
            Filter\DataFilter::class => InvokableFactory::class,

            Repository\CachedSearchResultRepository::class => ReflectionFactory::class,
            Repository\CraftingCategoryRepository::class => ReflectionFactory::class,
            Repository\IconFileRepository::class => ReflectionFactory::class,
            Repository\IconRepository::class => ReflectionFactory::class,
            Repository\ItemRepository::class => ReflectionFactory::class,
            Repository\MachineRepository::class => ReflectionFactory::class,
            Repository\ModRepository::class => ReflectionFactory::class,
            Repository\ModCombinationRepository::class => ReflectionFactory::class,
            Repository\RecipeRepository::class => ReflectionFactory::class,
            Repository\TranslationRepository::class => ReflectionFactory::class,

            // 3rd-party dependencies
            EntityManagerInterface::class => EntityManagerFactory::class,
        ],
    ],
];
