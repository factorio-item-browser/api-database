<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Constant\TranslationType;
use FactorioItemBrowser\Api\Database\Data\TranslationData;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Entity\Translation;

/**
 * The repository of the translation database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
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
     * Persists the translations to the combination, using optimized queries.
     * @param Combination $combination
     * @param array|Translation[] $translations
     * @throws DBALException
     */
    public function persistTranslationsToCombination(Combination $combination, array $translations): void
    {
        $parameters = [];
        $parametersCross = [];
        foreach ($translations as $translation) {
            $parameters[] = $translation->getId()->getBytes();
            $parameters[] = $translation->getLocale();
            $parameters[] = $translation->getType();
            $parameters[] = $translation->getName();
            $parameters[] = $translation->getValue();
            $parameters[] = $translation->getDescription();
            $parameters[] = $translation->getIsDuplicatedByMachine();
            $parameters[] = $translation->getIsDuplicatedByRecipe();

            $parametersCross[] = $combination->getId()->getBytes();
            $parametersCross[] = $translation->getId()->getBytes();
        }

        $this->executeNativeSql(
            "INSERT IGNORE INTO `Translation` "
                . "(`id`,`locale`,`type`,`name`,`value`,`description`,`isDuplicatedByMachine`,`isDuplicatedByRecipe`)"
                . "VALUES {$this->buildParameterPlaceholders(count($translations), 8)}",
            $parameters
        );

        $this->executeNativeSql(
            "DELETE FROM `CombinationXTranslation` WHERE `combinationId` = ?",
            [$combination->getId()->getBytes()]
        );

        $this->executeNativeSql(
            "INSERT INTO `CombinationXTranslation` (`combinationId`, `translationId`) "
                . "VALUES {$this->buildParameterPlaceholders(count($translations), 2)}",
            $parametersCross
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




    /**
     * Finds the translation data with the specified types and names.
     * @param string $locale The locale to prefer in the results.
     * @param array|string[][] $namesByTypes The names to search, grouped by their types.
     * @param array|int[] $modCombinationIds The IDs of the mod combinations, or empty to use all translations.
     * @return array|TranslationData[]
     */
    public function findDataByTypesAndNames(string $locale, array $namesByTypes, array $modCombinationIds = []): array
    {
        $columns = [
            't.locale AS locale',
            't.type AS type',
            't.name AS name',
            't.value AS value',
            't.description AS description',
            't.isDuplicatedByRecipe AS isDuplicatedByRecipe',
            't.isDuplicatedByMachine AS isDuplicatedByMachine',
            'mc.order AS order'
        ];

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select($columns)
                     ->from(Translation::class, 't')
                     ->innerJoin('t.modCombination', 'mc')
                     ->andWhere('t.locale IN (:locales)')
                     ->setParameter('locales', [$locale, 'en']);

        $conditions = [];
        foreach ($namesByTypes as $type => $names) {
            if (count($names) > 0) {
                $index = count($conditions);
                switch ($type) {
                    case TranslationType::RECIPE:
                        // Special case: Recipes may re-use the translations provided by the item with the same name.
                        $conditions[] = '((t.type = :type' . $index . ' OR t.isDuplicatedByRecipe = 1) '
                            . 'AND t.name IN (:names' . $index . '))';
                        break;

                    case TranslationType::MACHINE:
                        // Special case: Machines may re-use the translations provided by the item with the same name.
                        $conditions[] = '((t.type = :type' . $index . ' OR t.isDuplicatedByMachine = 1) '
                            . 'AND t.name IN (:names' . $index . '))';
                        break;

                    default:
                        $conditions[] = '(t.type = :type' . $index . ' AND t.name IN (:names' . $index . '))';
                        break;
                }
                $queryBuilder->setParameter('type' . $index, $type)
                             ->setParameter('names' . $index, array_values($names));
            }
        }

        $result = [];
        if (count($conditions) > 0) {
            $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');

            if (count($modCombinationIds) > 0) {
                $queryBuilder->andWhere('(t.modCombination IN (:modCombinationIds) OR t.type = :typeMod)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds))
                             ->setParameter('typeMod', 'mod');
            }

            $result = $this->mapTranslationDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Maps the query result to instances of TranslationData.
     * @param array $translationData
     * @return array|TranslationData[]
     */
    protected function mapTranslationDataResult(array $translationData): array
    {
        $result = [];
        foreach ($translationData as $data) {
            $result[] = TranslationData::createFromArray($data);
        }
        return $result;
    }

    /**
     * Finds the types and names matching the specified keywords.
     * @param string $locale
     * @param array|string[] $keywords
     * @param array|int[] $modCombinationIds
     * @return array|TranslationPriorityData[]
     */
    public function findDataByKeywords(string $locale, array $keywords, array $modCombinationIds = []): array
    {
        $result = [];
        if (count($keywords) > 0) {
            $concat = 'LOWER(CONCAT(t.type, t.name, t.value, t.description))';
            $priorityCase = 'CASE WHEN t.locale = :localePrimary THEN :priorityPrimary '
                . 'WHEN t.locale = :localeSecondary THEN :prioritySecondary ELSE :priorityAny END';

            $columns = [
                't.type AS type',
                't.name AS name',
                'MIN(' . $priorityCase . ') AS priority'
            ];

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select($columns)
                         ->from(Translation::class, 't')
                         ->andWhere('t.type IN (:types)')
                         ->addGroupBy('t.type')
                         ->addGroupBy('t.name')
                         ->setParameter('localePrimary', $locale)
                         ->setParameter('localeSecondary', 'en')
                         ->setParameter('priorityPrimary', SearchResultPriority::PRIMARY_LOCALE_MATCH)
                         ->setParameter('prioritySecondary', SearchResultPriority::SECONDARY_LOCALE_MATCH)
                         ->setParameter('priorityAny', SearchResultPriority::ANY_MATCH)
                         ->setParameter('types', [
                             TranslationType::ITEM,
                             TranslationType::FLUID,
                             TranslationType::RECIPE
                         ]);

            $index = 0;
            foreach ($keywords as $keyword) {
                $queryBuilder->andWhere($concat . ' LIKE :keyword' . $index)
                             ->setParameter('keyword' . $index, '%' . addcslashes($keyword, '\\%_') . '%');
                ++$index;
            }

            if (count($modCombinationIds) > 0) {
                $queryBuilder->innerJoin('t.modCombination', 'mc')
                             ->andWhere('mc.id IN (:modCombinationIds)')
                             ->setParameter('modCombinationIds', array_values($modCombinationIds));
            }

            $result = $this->mapTranslationPriorityDataResult($queryBuilder->getQuery()->getResult());
        }
        return $result;
    }

    /**
     * Maps the query result to instances of TranslationPriorityData.
     * @param array $translationPriorityData
     * @return array|TranslationPriorityData[]
     */
    protected function mapTranslationPriorityDataResult(array $translationPriorityData): array
    {
        $result = [];
        foreach ($translationPriorityData as $data) {
            $result[] = TranslationPriorityData::createFromArray($data);
        }
        return $result;
    }
}
