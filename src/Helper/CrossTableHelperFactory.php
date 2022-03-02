<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Entity\EntityWithId;
use FactorioItemBrowser\Api\Database\Exception\CrossTableException;

/**
 * The factory for the CrossTableHelper.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CrossTableHelperFactory
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Creates the CrossTableHelper managing the provided entity.
     * @template T of EntityWithId
     * @param class-string<T> $entityClass
     * @return CrossTableHelper<T>
     * @throws CrossTableException
     */
    public function createForEntity(string $entityClass): CrossTableHelper
    {
        try {
            $entityMetadata = $this->entityManager->getClassMetadata($entityClass);
            $combinationMapping = $entityMetadata->getAssociationMapping('combinations');
            if (!isset($combinationMapping['mappedBy'])) {
                throw new CrossTableException($entityClass, 'Missing combinations field');
            }
            $columName = strval($combinationMapping['mappedBy']);

            $combinationMetadata = $this->entityManager->getClassMetadata(Combination::class);
            $entityMapping = $combinationMetadata->getAssociationMapping($columName);
            if (!isset($entityMapping['joinTable']) || !is_array($entityMapping['joinTable'])) {
                throw new CrossTableException($entityClass, 'Missing joinTable association');
            }

            $crossTableName = strval($entityMapping['joinTable']['name'] ?? '');
            $combinationColumnName = strval($entityMapping['joinTable']['joinColumns'][0]['name'] ?? '');
            $entityColumnName = strval($entityMapping['joinTable']['inverseJoinColumns'][0]['name']);

            /** @var CrossTableHelper<T> $helper */
            $helper = new CrossTableHelper(
                $this->entityManager,
                $crossTableName,
                $combinationColumnName,
                $entityColumnName,
            );
            return $helper;
        } catch (MappingException $e) {
            throw new CrossTableException($entityClass, $e->getMessage(), $e);
        }
    }
}
