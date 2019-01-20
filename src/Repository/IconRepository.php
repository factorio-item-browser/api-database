<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use FactorioItemBrowser\Api\Database\Data\IconData;
use FactorioItemBrowser\Api\Database\Entity\Icon;

/**
 * The repository class of the icon database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IconRepository extends AbstractRepository
{
    /**
     * Finds the data of the specified entities.
     * @param array|string[][] $namesByTypes
     * @param array|int[] $modCombinationIds
     * @return array|IconData[]
     */
    public function findDataByTypesAndNames(array $namesByTypes, array $modCombinationIds = []): array
    {
        $columns = [
            'i.id AS id',
            'IDENTITY(i.file) AS hash',
            'i.type AS type',
            'i.name AS name',
            'mc.order AS order'
        ];

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select($columns)
                     ->from(Icon::class, 'i')
                     ->innerJoin('i.modCombination', 'mc');

        $index = 0;
        $conditions = [];
        foreach ($namesByTypes as $type => $names) {
            $conditions[] = '(i.type = :type' . $index . ' AND i.name IN (:names' . $index . '))';
            $queryBuilder->setParameter('type' . $index, $type)
                         ->setParameter('names' . $index, array_values($names));
            ++$index;
        }

        $result = [];
        if ($index > 0) {
            $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $this->mapIconDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Finds the data of the icons with the specified hashes.
     * @param array|string[] $hashes
     * @param array|int[] $modCombinationIds
     * @return array|IconData[]
     */
    public function findDataByHashes(array $hashes, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($hashes) > 0) {
            $columns = [
                'i.id AS id',
                'IDENTITY(i.file) AS hash',
                'i.type AS type',
                'i.name AS name',
                'mc.order AS order'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Icon::class, 'i')
                         ->innerJoin('i.modCombination', 'mc')
                         ->andWhere('i.file IN (:hashes)')
                         ->setParameter('hashes', array_map('hex2bin', array_values($hashes)));

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $this->mapIconDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Maps the query result to instances of IconData.
     * @param array $iconData
     * @return array|IconData[]
     */
    protected function mapIconDataResult(array $iconData): array
    {
        $result = [];
        foreach ($iconData as $data) {
            $result[] = IconData::createFromArray($data);
        }
        return $result;
    }

    /**
     * Finds the icons by their id.
     * @param array|int[] $ids
     * @return array|Icon[]
     */
    public function findByIds(array $ids): array
    {
        $result = [];
        if (count($ids) > 0) {
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('i')
                         ->from(Icon::class, 'i')
                         ->andWhere('i.id IN (:ids)')
                         ->setParameter('ids', array_values($ids));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }
}
