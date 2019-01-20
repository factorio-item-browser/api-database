<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * The abstract factory for the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AbstractRepositoryFactory implements FactoryInterface
{
    /**
     * Create an object
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AbstractRepository
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        return new $requestedName($entityManager);
    }
}
