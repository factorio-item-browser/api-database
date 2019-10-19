<?php

declare(strict_types=1);

/**
 * The configuration of the database dependencies.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace FactorioItemBrowser\Api\Database;

use BluePsyduck\ZendAutoWireFactory\AutoWireFactory;
use ContainerInteropDoctrine\EntityManagerFactory;
use Doctrine\ORM\EntityManagerInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'dependencies' => [
        'factories'  => [
            Filter\DataFilter::class => InvokableFactory::class,

            Repository\CachedSearchResultRepository::class => AutoWireFactory::class,
            Repository\CombinationRepository::class => AutoWireFactory::class,
            Repository\CraftingCategoryRepository::class => AutoWireFactory::class,
            Repository\IconImageRepository::class => AutoWireFactory::class,
            Repository\IconRepository::class => AutoWireFactory::class,
            Repository\ItemRepository::class => AutoWireFactory::class,
            Repository\MachineRepository::class => AutoWireFactory::class,
            Repository\ModRepository::class => AutoWireFactory::class,
            Repository\RecipeRepository::class => AutoWireFactory::class,
            Repository\TranslationRepository::class => AutoWireFactory::class,

            // 3rd-party dependencies
            EntityManagerInterface::class => EntityManagerFactory::class,
        ],
    ],
];
