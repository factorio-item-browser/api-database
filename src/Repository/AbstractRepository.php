<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * The abstract class of the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AbstractRepository
{
    /**
     * The entity manager.
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * Initializes the repository.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Maps the ids to their parameter values.
     * @param array<UuidInterface> $ids
     * @return array<string>
     */
    protected function mapIdsToParameterValues(array $ids): array
    {
        return array_map(function (UuidInterface $id): string {
            return $id->getBytes();
        }, $ids);
    }
}
