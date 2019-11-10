<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Entity\Translation;
use FactorioItemBrowser\Common\Constant\EntityType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The repository of the translation database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 *
 * @method array|Translation[] findByIds(array|UuidInterface[] $ids)
 */
class TranslationRepository extends AbstractIdRepositoryWithOrphans
{
    /**
     * Returns the entity class this repository manages.
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Translation::class;
    }

    /**
     * Adds the conditions to the query builder for detecting orphans.
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     */
    protected function addOrphanConditions(QueryBuilder $queryBuilder, string $alias): void
    {
        $queryBuilder->leftJoin("{$alias}.combinations", 'c')
                     ->andWhere('c.id IS NULL');
    }

    /**
     * Finds translations with the specified types and names.
     * @param UuidInterface $combinationId
     * @param string $locale The locale to prefer in the results.
     * @param NamesByTypes $namesByTypes The names to search, grouped by their types.
     * @return array|Translation[]
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
            switch ($type) {
                case EntityType::RECIPE:
                    // Special case: Recipes may re-use the translations provided by the item with the same name.
                    $conditions[] = "((t.type = :type{$i} OR t.isDuplicatedByRecipe = 1) AND t.name IN (:names{$i}))";
                    break;

                case EntityType::MACHINE:
                    // Special case: Machines may re-use the translations provided by the item with the same name.
                    $conditions[] = "((t.type = :type{$i} OR t.isDuplicatedByMachine = 1) AND t.name IN (:names{$i}))";
                    break;

                default:
                    $conditions[] = "(t.type = :type{$i} AND t.name IN (:names{$i}))";
                    break;
            }
            $queryBuilder->setParameter("type{$i}", $type)
                         ->setParameter("names{$i}", array_values($names));
        }
        $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Finds the types and names matching the specified keywords.
     * @param UuidInterface $combinationId
     * @param string $locale
     * @param array|string[] $keywords
     * @return array|TranslationPriorityData[]
     */
    public function findDataByKeywords(UuidInterface $combinationId, string $locale, array $keywords): array
    {
        if (count($keywords) === 0) {
            return [];
        }

        $searchField = 'LOWER(CONCAT(t.type, t.name, t.value, t.description))';
        $priority = 'CASE WHEN t.locale = :localePrimary THEN :priorityPrimary '
            . 'WHEN t.locale = :localeSecondary THEN :prioritySecondary ELSE :priorityAny END';

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
                     ->addGroupBy('t.type')
                     ->addGroupBy('t.name')
                     ->setParameter('combinationId', $combinationId, UuidBinaryType::NAME)
                     ->setParameter('localePrimary', $locale)
                     ->setParameter('localeSecondary', 'en')
                     ->setParameter('priorityPrimary', SearchResultPriority::PRIMARY_LOCALE_MATCH)
                     ->setParameter('prioritySecondary', SearchResultPriority::SECONDARY_LOCALE_MATCH)
                     ->setParameter('priorityAny', SearchResultPriority::ANY_MATCH)
                     ->setParameter('types', [
                         EntityType::ITEM,
                         EntityType::FLUID,
                         EntityType::RECIPE,
                     ]);

        foreach (array_values($keywords) as $index => $keyword) {
            $queryBuilder->andWhere("{$searchField} LIKE :keyword{$index}")
                         ->setParameter("keyword{$index}", '%' . addcslashes(strtolower($keyword), '\\%_') . '%');
        }

        return $this->mapTranslationPriorityDataResult($queryBuilder->getQuery()->getResult());
    }

    /**
     * Maps the query result to instances of TranslationPriorityData.
     * @param array $translationPriorityData
     * @return array|TranslationPriorityData[]
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

    /**
     * Persists the translations to the combination, using optimized queries.
     * @param UuidInterface $combinationId
     * @param array|Translation[] $translations
     * @throws DBALException
     */
    public function persistTranslationsToCombination(UuidInterface $combinationId, array $translations): void
    {
        $this->insertTranslations($translations);
        $this->clearCrossTable($combinationId);
        $this->insertIntoCrossTable($combinationId, $translations);
    }

    /**
     * Inserts the translations into the database.
     * @param array|Translation[] $translations
     * @throws DBALException
     */
    protected function insertTranslations(array $translations): void
    {
        if (count($translations) === 0) {
            return;
        }

        $parameters = [];
        foreach ($translations as $translation) {
            $parameters[] = $translation->getId()->getBytes();
            $parameters[] = $translation->getLocale();
            $parameters[] = $translation->getType();
            $parameters[] = $translation->getName();
            $parameters[] = $translation->getValue();
            $parameters[] = $translation->getDescription();
            $parameters[] = $translation->getIsDuplicatedByMachine();
            $parameters[] = $translation->getIsDuplicatedByRecipe();
        }

        $this->executeNativeSql(
            'INSERT IGNORE INTO `Translation` '
                . '(`id`,`locale`,`type`,`name`,`value`,`description`,`isDuplicatedByMachine`,`isDuplicatedByRecipe`) '
                . "VALUES {$this->buildParameterPlaceholders(count($translations), 8)}",
            $parameters
        );
    }

    /**
     * Clears the cross table to the specified combination.
     * @param UuidInterface $combinationId
     * @throws DBALException
     */
    protected function clearCrossTable(UuidInterface $combinationId): void
    {
        $this->executeNativeSql(
            'DELETE FROM `CombinationXTranslation` WHERE `combinationId` = ?',
            [$combinationId->getBytes()]
        );
    }

    /**
     * Inserts the translations into the cross table to the specified combination.
     * @param UuidInterface $combinationId
     * @param array $translations
     * @throws DBALException
     */
    protected function insertIntoCrossTable(UuidInterface $combinationId, array $translations): void
    {
        if (count($translations) === 0) {
            return;
        }

        $parameters = [];
        foreach ($translations as $translation) {
            $parameters[] = $combinationId->getBytes();
            $parameters[] = $translation->getId()->getBytes();
        }

        $this->executeNativeSql(
            'INSERT INTO `CombinationXTranslation` (`combinationId`, `translationId`) '
            . "VALUES {$this->buildParameterPlaceholders(count($translations), 2)}",
            $parameters
        );
    }

    /**
     * Builds the placeholders for all the parameters to insert.
     * @param int $numberOfRows
     * @param int $numberOfValues
     * @return string
     */
    protected function buildParameterPlaceholders(int $numberOfRows, int $numberOfValues): string
    {
        $line = '(' . implode(',', array_fill(0, $numberOfValues, '?')) . ')';
        return implode(',', array_fill(0, $numberOfRows, $line));
    }

    /**
     * Executes a native query on the database.
     * @param string $query
     * @param array $parameters
     * @throws DBALException
     */
    protected function executeNativeSql(string $query, array $parameters): void
    {
        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->execute($parameters);
    }
}
