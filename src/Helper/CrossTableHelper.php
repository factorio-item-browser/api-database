<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\UnexpectedResultException;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Entity\EntityWithId;

/**
 * The class helping with the cross-tables to the combination on a low level.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @template T of EntityWithId
 */
class CrossTableHelper
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $crossTableName,
        private readonly string $combinationColumnName,
        private readonly string $entityColumnName,
    ) {
    }

    /**
     * Clears the cross-table from all entries related to the specified combination.
     */
    public function clear(Combination $combination): void
    {
        $sql = "DELETE FROM {$this->crossTableName} WHERE {$this->combinationColumnName} = :combinationId";

        $query = $this->entityManager->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameter('combinationId', $combination->getId(), CustomTypes::UUID);

        $query->execute();
    }

    /**
     * Inserts the entities into the cross-table for the provided combination.
     * @param array<T> $entities
     */
    public function insert(Combination $combination, array $entities): void
    {
        if (count($entities) === 0) {
            return;
        }

        $values = implode(',', array_fill(0, count($entities), '(?,?)'));
        $sql = "INSERT IGNORE INTO {$this->crossTableName} ({$this->combinationColumnName}, {$this->entityColumnName}) "
            . "VALUES $values";

        $index = 0;
        $parameters = new ArrayCollection();
        foreach ($entities as $entity) {
            $parameters->add(new Parameter((string) $index++, $combination->getId(), CustomTypes::UUID));
            $parameters->add(new Parameter((string) $index++, $entity->getId(), CustomTypes::UUID));
        }

        $query = $this->entityManager->createNativeQuery($sql, new ResultSetMapping());
        $query->setParameters($parameters);

        $query->execute();
    }

    /**
     * Counts the entities assigned to the provided combination.
     * @param Combination $combination
     * @return int
     */
    public function count(Combination $combination): int
    {
        $sql = "SELECT COUNT(1) AS c FROM {$this->crossTableName} "
            . "WHERE {$this->combinationColumnName} = :combinationId";

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('c', 'c');

        $query = $this->entityManager->createNativeQuery($sql, $rsm);
        $query->setParameter('combinationId', $combination->getId(), CustomTypes::UUID);

        try {
            return intval($query->getSingleScalarResult());
        } catch (UnexpectedResultException) {
            return 0;
        }
    }
}
