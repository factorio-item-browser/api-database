<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Repository;

use Doctrine\ORM\EntityRepository;
use FactorioItemBrowser\Api\Database\Constant\EntityType;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\TranslationData;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;

/**
 * The repository of the translation database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslationRepository extends EntityRepository
{
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

        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder->select($columns)
                     ->innerJoin('t.modCombination', 'mc')
                     ->andWhere('t.locale IN (:locales)')
                     ->setParameter('locales', [$locale, 'en']);

        $conditions = [];
        foreach ($namesByTypes as $type => $names) {
            if (count($names) > 0) {
                $index = count($conditions);
                switch ($type) {
                    case EntityType::RECIPE:
                        // Special case: Recipes may re-use the translations provided by the item with the same name.
                        $conditions[] = '((t.type = :type' . $index . ' OR t.isDuplicatedByRecipe = 1) '
                            . 'AND t.name IN (:names' . $index . '))';
                        break;

                    case EntityType::MACHINE:
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

            $queryBuilder = $this->createQueryBuilder('t');
            $queryBuilder->select($columns)
                         ->andWhere('t.type IN (:types)')
                         ->addGroupBy('t.type')
                         ->addGroupBy('t.name')
                         ->setParameter('localePrimary', $locale)
                         ->setParameter('localeSecondary', 'en')
                         ->setParameter('priorityPrimary', SearchResultPriority::PRIMARY_LOCALE_MATCH)
                         ->setParameter('prioritySecondary', SearchResultPriority::SECONDARY_LOCALE_MATCH)
                         ->setParameter('priorityAny', SearchResultPriority::ANY_MATCH)
                         ->setParameter('types', [EntityType::ITEM, EntityType::FLUID, EntityType::RECIPE]);

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
