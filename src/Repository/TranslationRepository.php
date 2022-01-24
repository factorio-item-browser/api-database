<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Entity\Translation;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansInterface;
use FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait;
use FactorioItemBrowser\Common\Constant\EntityType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository of the translation database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @implements FindByIdsInterface<Translation>
 */
class TranslationRepository extends AbstractRepository implements
    FindByIdsInterface,
    RemoveOrphansInterface
{
    /** @use FindByIdsTrait<Translation> */
    use FindByIdsTrait;
    /** @use RemoveOrphansTrait<Translation> */
    use RemoveOrphansTrait;

    protected function getEntityClass(): string
    {
        return Translation::class;
    }

    protected function addRemoveOrphansConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    /**
     * Finds translations with the specified types and names.
     * @return array<Translation>
     */
    public function findByTypesAndNames(UuidInterface $combinationId, string $locale, NamesByTypes $namesByTypes): array
    {
        if ($namesByTypes->isEmpty()) {
            return [];
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('t')
                     ->from(Translation::class, 't')
                     ->innerJoin('t.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->andWhere('t.locale IN (:locales)')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('locales', [$locale, 'en']);

        $conditions = [];
        foreach ($namesByTypes->toArray() as $type => $names) {
            $i = count($conditions);
            $conditions[] = "(t.type IN (:types{$i}) AND t.name IN (:names{$i}))";

            $types = match ($type) {
                EntityType::RECIPE => [EntityType::RECIPE, EntityType::FLUID, EntityType::ITEM],
                EntityType::MACHINE => [EntityType::MACHINE, EntityType::FLUID, EntityType::ITEM],
                default => [$type],
            };

            $queryBuilder->setParameter("types{$i}", $types)
                         ->setParameter("names{$i}", array_values($names));
        }
        $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');

        /** @var array<Translation> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $queryResult;
    }

    /**
     * Finds the types and names matching the specified keywords.
     * @param array<string> $keywords
     * @return array<TranslationPriorityData>
     */
    public function findDataByKeywords(UuidInterface $combinationId, string $locale, array $keywords): array
    {
        if (count($keywords) === 0) {
            return [];
        }

        $searchField = "LOWER(CONCAT(t.value, '|', t.description))";
        $priority = 'CASE WHEN t.locale = :localePrimary THEN :priorityPrimary ELSE :prioritySecondary END';
        $columns = [
            't.type AS type',
            't.name AS name',
            "MIN({$priority}) AS priority",
        ];

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select($columns)
                     ->from(Translation::class, 't')
                     ->innerJoin('t.combinations', 'c', 'WITH', 'c.id = :combinationId')
                     ->andWhere('t.type IN (:types)')
                     ->andWhere('t.locale IN (:localePrimary, :localeSecondary)')
                     ->addGroupBy('t.type')
                     ->addGroupBy('t.name')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('localePrimary', $locale)
                     ->setParameter('localeSecondary', 'en')
                     ->setParameter('priorityPrimary', SearchResultPriority::PRIMARY_LOCALE_MATCH)
                     ->setParameter('prioritySecondary', SearchResultPriority::SECONDARY_LOCALE_MATCH)
                     ->setParameter('types', [
                         EntityType::ITEM,
                         EntityType::FLUID,
                         EntityType::RECIPE,
                     ]);

        foreach (array_values($keywords) as $index => $keyword) {
            $queryBuilder->andWhere("{$searchField} LIKE :keyword{$index}")
                         ->setParameter("keyword{$index}", '%' . addcslashes(strtolower($keyword), '\\%_') . '%');
        }

        /** @var array<array{type: string, name: string, priority: string}> $queryResult */
        $queryResult = $queryBuilder->getQuery()->getResult();
        return $this->mapTranslationPriorityDataResult($queryResult);
    }

    /**
     * Maps the query result to instances of TranslationPriorityData.
     * @param array<array{type: string, name: string, priority: string}> $translationPriorityData
     * @return array<TranslationPriorityData>
     */
    protected function mapTranslationPriorityDataResult(array $translationPriorityData): array
    {
        $result = [];
        foreach ($translationPriorityData as $row) {
            $data = new TranslationPriorityData();
            $data->setType($row['type'])
                 ->setName($row['name'])
                 ->setPriority((int) $row['priority']);

            $result[] = $data;
        }
        return $result;
    }
}
