<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database;

use ContainerInteropDoctrine\EntityManagerFactory;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * The config provider of the API database library.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ConfigProvider
{
    /**
     * Returns the configuration of the library.
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'doctrine' => $this->getDoctrineConfig(),
        ];
    }

    /**
     * Returns the dependencies configuration.
     * @return array
     */
    public function getDependencyConfig(): array
    {
        return [
            EntityManager::class => EntityManagerFactory::class,
        ];
    }

    /**
     * Returns the doctrine configuration.
     * @return array
     */
    public function getDoctrineConfig(): array
    {
        return [
            'configuration' => [
                'orm_default' => [
                    'numeric_functions' => [
//                        'Rand' => Database\Functions\RandFunction::class,
                    ],
                ],
            ],
            'driver' => [
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                    'drivers' => [
                        'FactorioItemBrowser\Api\Database\Entity' => 'fib-api-database',
                    ],
                ],

                'fib-api-database' => [
                    'class' => AnnotationDriver::class,
                    'paths' => [
                        __DIR__ . '/../../src/Entity',
                    ],
                ],
            ],
        ];
    }
}
