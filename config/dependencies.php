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
            EntityManagerInterface::class => EntityManagerFactory::class,
        ],
    ],
];
