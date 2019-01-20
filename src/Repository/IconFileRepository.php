<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityRepository;
use FactorioItemBrowser\Api\Database\Entity\IconFile;

/**
 * The repository class of the icon file database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IconFileRepository extends EntityRepository implements RepositoryWithOrphansInterface
{
    /**
     * Finds the icon files with the specified hashes.
     * @param array|string[] $hashes
     * @return array|IconFile[]
     */
    public function findByHashes(array $hashes): array
    {
        $result = [];
        if (count($hashes) > 0) {
            $queryBuilder = $this->createQueryBuilder('if');
            $queryBuilder->andWhere('if.hash IN (:hashes)')
                         ->setParameter('hashes', array_values(array_map('hex2bin', $hashes)));

            $result = $queryBuilder->getQuery()->getResult();
        }
        return $result;
    }

    /**
     * Removes any orphaned icon files, i.e. icon files no longer used by any icon.
     */
    public function removeOrphans(): void
    {
        $hashes = $this->findOrphanedHashes();
        if (count($hashes) > 0) {
            $this->removeHashes($hashes);
        }
    }

    /**
     * Returns the hashes of orphaned icon files, which are no longer used by any icon.
     * @return array|string[]
     */
    protected function findOrphanedHashes(): array
    {
        $queryBuilder = $this->createQueryBuilder('if');
        $queryBuilder->select('if.hash AS hash')
                     ->leftJoin('if.icons', 'i')
                     ->andWhere('i.id IS NULL');

        $result = [];
        foreach ($queryBuilder->getQuery()->getResult() as $data) {
            $result[] = $data['hash'];
        }
        return $result;
    }

    /**
     * Removes the icon files with the specified hashes from the database.
     * @param array|string[] $hashes
     */
    protected function removeHashes(array $hashes): void
    {
        $queryBuilder = $this->createQueryBuilder('if');
        $queryBuilder->delete($this->getEntityName(), 'if')
                     ->andWhere('if.hash IN (:hashes)')
                     ->setParameter('hashes', array_values($hashes));

        $queryBuilder->getQuery()->execute();
    }
}
