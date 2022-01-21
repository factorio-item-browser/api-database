<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Ramsey\Uuid\UuidInterface;

/**
 * The abstract class of the repositories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class AbstractRepository
{
    protected readonly EntityManagerInterface $entityManager;

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

    /**
     * Unwraps the query, catching the impossible non-unique exception and transforming it to null.
     * @template T of object
     * @param AbstractQuery $query
     * @param class-string<T> $className
     * @return ?T
     */
    protected function unwrapOneOrNullResult(AbstractQuery $query, string $className): mixed
    {
        try {
            $result = $query->getOneOrNullResult();
            return $result instanceof $className ? $result : null;
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
