<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Entity\Icon;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository class of the icon database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IconRepository extends AbstractRepository
{
    /**
     * Finds the icons of the specified types and names.
     * @param UuidInterface $combinationId
     * @param array|string[][] $namesByTypes
     * @return array|Icon[]
     */
    public function findByTypesAndNames(UuidInterface $combinationId, array $namesByTypes): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Icon::class, 'i')
                     ->innerJoin('i.combination', 'c', 'WITH', 'c.id = :combinationId')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME);

        $index = 0;
        foreach ($namesByTypes as $type => $names) {
            if (count($names) === 0) {
                continue;
            }

            $queryBuilder->orWhere("i.type = :type{$index} AND i.name IN (:names{$index})")
                         ->setParameter("type{$index}", $type)
                         ->setParameter("names{$index}", array_values($names));
            ++$index;
        }

        if ($index === 0) {
            return [];
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Finds the icons using one of the image ids.
     * @param UuidInterface $combinationId
     * @param array|UuidInterface[] $imageIds
     * @return array|Icon[]
     */
    public function findByImageIds(UuidInterface $combinationId, array $imageIds): array
    {
        if (count($imageIds) === 0) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('i')
                     ->from(Icon::class, 'i')
                     ->innerJoin('i.combination', 'c', 'WITH', 'c.id = :combinationId')
                     ->andWhere('i.image IN (:imageIds)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('imageIds', $this->mapIdsToParameterValues($imageIds));

        return $queryBuilder->getQuery()->getResult();
    }
}
