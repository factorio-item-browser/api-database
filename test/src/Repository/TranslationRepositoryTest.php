<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\Common\Test\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Constant\TranslationType;
use FactorioItemBrowser\Api\Database\Data\TranslationData;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Repository\TranslationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the TranslationRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\TranslationRepository
 */
class TranslationRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Provides the data for the findDataByTypesAndNames test.
     * @return array
     */
    public function provideFindDataByTypesAndNames(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByTypesAndNames method.
     * @param bool $withNamesByTypes
     * @param bool $withModCombinationIds
     * @covers ::findDataByTypesAndNames
     * @dataProvider provideFindDataByTypesAndNames
     */
    public function testFindDataByTypesAndNames(bool $withNamesByTypes, bool $withModCombinationIds): void
    {
        $locale = 'xyz';
        $namesByTypes = [];
        $condition = '';
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withNamesByTypes ? [['locale' => 'def']] : [];
        $dataResult = $withNamesByTypes ? [$this->createMock(TranslationData::class)] : [];

        if ($withNamesByTypes) {
            $namesByTypes = [
                TranslationType::RECIPE => ['abc', 'def'],
                TranslationType::MACHINE => ['ghi'],
                TranslationType::ITEM => ['jkl', 'mno']
            ];

            $condition = '(((t.type = :type0 OR t.isDuplicatedByRecipe = 1) AND t.name IN (:names0))'
                . ' OR ((t.type = :type1 OR t.isDuplicatedByMachine = 1) AND t.name IN (:names1))'
                . ' OR (t.type = :type2 AND t.name IN (:names2)))';
        }

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withNamesByTypes ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with([
                         't.locale AS locale',
                         't.type AS type',
                         't.name AS name',
                         't.value AS value',
                         't.description AS description',
                         't.isDuplicatedByRecipe AS isDuplicatedByRecipe',
                         't.isDuplicatedByMachine AS isDuplicatedByMachine',
                         'mc.order AS order'
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with('t.modCombination', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNamesByTypes ? $withModCombinationIds ? 3 : 2 : 1))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['t.locale IN (:locales)'],
                         [$condition],
                         ['(t.modCombination IN (:modCombinationIds) OR t.type = :typeMod)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNamesByTypes ? $withModCombinationIds ? 9 : 7 : 1))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['locales', [$locale, 'en']],
                         ['type0', TranslationType::RECIPE],
                         ['names0', ['abc', 'def']],
                         ['type1', TranslationType::MACHINE],
                         ['names1', ['ghi']],
                         ['type2', TranslationType::ITEM],
                         ['names2', ['jkl', 'mno']],
                         ['modCombinationIds', $modCombinationIds],
                         ['typeMod', 'mod']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withNamesByTypes ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var TranslationRepository|MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->setMethods(['createQueryBuilder', 'mapTranslationDataResult'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('t')
                   ->willReturn($queryBuilder);
        $repository->expects($withNamesByTypes ? $this->once() : $this->never())
                   ->method('mapTranslationDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByTypesAndNames($locale, $namesByTypes, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Tests the mapTranslationDataResult method.
     * @throws ReflectionException
     * @covers ::mapTranslationDataResult
     */
    public function testMapTranslationDataResult(): void
    {
        $translationData = [
            ['locale' => 'abc'],
            ['locale' => 'def']
        ];
        $expectedResult = [
            (new TranslationData())->setLocale('abc'),
            (new TranslationData())->setLocale('def'),
        ];

        /* @var TranslationRepository $repository */
        $repository = $this->createMock(TranslationRepository::class);

        $result = $this->invokeMethod($repository, 'mapTranslationDataResult', $translationData);
        $this->assertEquals($expectedResult, $result);
    }
    
    /**
     * Provides the data for the findDataByKeywords test.
     * @return array
     */
    public function provideFindDataByKeywords(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByKeywords method.
     * @param bool $withKeywords
     * @param bool $withModCombinationIds
     * @covers ::findDataByKeywords
     * @dataProvider provideFindDataByKeywords
     */
    public function testFindDataByKeywords(bool $withKeywords, bool $withModCombinationIds): void
    {
        $locale = 'xyz';
        $keywords = $withKeywords ? ['foo', 'b_a\\r%'] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withKeywords ? [['abc' => 'def']] : [];
        $dataResult = $withKeywords ? [$this->createMock(TranslationPriorityData::class)] : [];

        $priorityColumn = 'MIN(CASE WHEN t.locale = :localePrimary THEN :priorityPrimary '
            . 'WHEN t.locale = :localeSecondary THEN :prioritySecondary ELSE :priorityAny END) AS priority';

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withKeywords ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'andWhere', 'addGroupBy', 'setParameter', 'innerJoin', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('select')
                     ->with([
                         't.type AS type',
                         't.name AS name',
                         $priorityColumn
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withKeywords ? $withModCombinationIds ? 4 : 3 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['t.type IN (:types)'],
                         ['LOWER(CONCAT(t.type, t.name, t.value, t.description)) LIKE :keyword0'],
                         ['LOWER(CONCAT(t.type, t.name, t.value, t.description)) LIKE :keyword1'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withKeywords ? $this->exactly(2) : $this->never())
                     ->method('addGroupBy')
                     ->withConsecutive(
                         ['t.type'],
                         ['t.name']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withKeywords ? $withModCombinationIds ? 9 : 8 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['localePrimary', $locale],
                         ['localeSecondary', 'en'],
                         ['priorityPrimary', SearchResultPriority::PRIMARY_LOCALE_MATCH],
                         ['prioritySecondary', SearchResultPriority::SECONDARY_LOCALE_MATCH],
                         ['priorityAny', SearchResultPriority::ANY_MATCH],
                         ['types', [TranslationType::ITEM, TranslationType::FLUID, TranslationType::RECIPE]],
                         ['keyword0', '%foo%'],
                         ['keyword1', '%b\\_a\\\\r\\%%'],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects(($withKeywords && $withModCombinationIds) ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('t.modCombination', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var TranslationRepository|MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->setMethods(['createQueryBuilder', 'mapTranslationPriorityDataResult'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withKeywords ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('t')
                   ->willReturn($queryBuilder);
        $repository->expects($withKeywords ? $this->once() : $this->never())
                   ->method('mapTranslationPriorityDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);


        $result = $repository->findDataByKeywords($locale, $keywords, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Tests the mapTranslationPriorityDataResult method.
     * @throws ReflectionException
     * @covers ::mapTranslationPriorityDataResult
     */
    public function testMapTranslationPriorityDataResult(): void
    {
        $translationPriorityData = [
            ['type' => 'abc'],
            ['type' => 'def']
        ];
        $expectedResult = [
            (new TranslationPriorityData())->setType('abc'),
            (new TranslationPriorityData())->setType('def'),
        ];

        /* @var TranslationRepository $repository */
        $repository = $this->createMock(TranslationRepository::class);

        $result = $this->invokeMethod($repository, 'mapTranslationPriorityDataResult', $translationPriorityData);
        $this->assertEquals($expectedResult, $result);
    }
}
