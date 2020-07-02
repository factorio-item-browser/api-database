<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use FactorioItemBrowser\Api\Database\Entity\Translation;
use FactorioItemBrowser\Api\Database\Repository\TranslationRepository;
use FactorioItemBrowser\Common\Constant\EntityType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;
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
     * The mocked entity manager.
     * @var EntityManagerInterface&MockObject
     */
    protected $entityManager;

    /**
     * Sets up the test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * Tests the getEntityClass method.
     * @throws ReflectionException
     * @covers ::getEntityClass
     */
    public function testGetEntityClass(): void
    {
        $repository = new TranslationRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(Translation::class, $result);
    }

    /**
     * Tests the addOrphanConditions method.
     * @throws ReflectionException
     * @covers ::addOrphanConditions
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with($this->identicalTo('abc.combinations'), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id IS NULL'))
                     ->willReturnSelf();

        $repository = new TranslationRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }

    /**
     * Tests the findByTypesAndNames method.
     * @covers ::findByTypesAndNames
     */
    public function testFindByTypesAndNames(): void
    {
        $locale = 'abc';

        /* @var NamesByTypes&MockObject $namesByTypes */
        $namesByTypes = $this->createMock(NamesByTypes::class);
        $namesByTypes->expects($this->once())
                     ->method('isEmpty')
                     ->willReturn(false);
        $namesByTypes->expects($this->once())
                     ->method('toArray')
                     ->willReturn([
                         EntityType::RECIPE => ['def', 'ghi'],
                         EntityType::MACHINE => ['jkl'],
                         EntityType::ITEM => ['mno', 'pqr']
                     ]);

        $expectedCondition = '(((t.type = :type0 OR t.isDuplicatedByRecipe = 1) AND t.name IN (:names0))'
            . ' OR ((t.type = :type1 OR t.isDuplicatedByMachine = 1) AND t.name IN (:names1))'
            . ' OR (t.type = :type2 AND t.name IN (:names2)))';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Translation::class),
            $this->createMock(Translation::class),
        ];

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('t'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Translation::class), $this->identicalTo('t'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('t.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('t.locale IN (:locales)')],
                         [$this->identicalTo($expectedCondition)]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(8))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('locales'),
                             $this->identicalTo([$locale, 'en'])
                         ],
                         [
                             $this->identicalTo('type0'),
                             $this->identicalTo(EntityType::RECIPE)
                         ],
                         [
                             $this->identicalTo('names0'),
                             $this->identicalTo(['def', 'ghi'])
                         ],
                         [
                             $this->identicalTo('type1'),
                             $this->identicalTo(EntityType::MACHINE)
                         ],
                         [
                             $this->identicalTo('names1'),
                             $this->identicalTo(['jkl'])
                         ],
                         [
                             $this->identicalTo('type2'),
                             $this->identicalTo(EntityType::ITEM)
                         ],
                         [
                             $this->identicalTo('names2'),
                             $this->identicalTo(['mno', 'pqr'])
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new TranslationRepository($this->entityManager);
        $result = $repository->findByTypesAndNames($combinationId, $locale, $namesByTypes);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByTypesAndNames method.
     * @covers ::findByTypesAndNames
     */
    public function testFindByTypesAndNamesWithoutConditions(): void
    {
        $locale = 'abc';

        /* @var NamesByTypes&MockObject $namesByTypes */
        $namesByTypes = $this->createMock(NamesByTypes::class);
        $namesByTypes->expects($this->once())
                     ->method('isEmpty')
                     ->willReturn(true);

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $repository = new TranslationRepository($this->entityManager);
        $result = $repository->findByTypesAndNames($combinationId, $locale, $namesByTypes);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findDataByKeywords method.
     * @covers ::findDataByKeywords
     */
    public function testFindDataByKeywords(): void
    {
        $locale = 'abc';
        $keywords = ['foo', 'b_a\\r%'];
        $priority = 'CASE WHEN t.locale = :localePrimary THEN :priorityPrimary ELSE :prioritySecondary END';
        $searchField = "LOWER(CONCAT(t.value, '|', t.description))";

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $this->createMock(UuidInterface::class)],
            ['id' => $this->createMock(UuidInterface::class)],
        ];
        $mappedResult = [
            $this->createMock(TranslationPriorityData::class),
            $this->createMock(TranslationPriorityData::class),
        ];

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo([
                         't.type AS type',
                         't.name AS name',
                         "MIN({$priority}) AS priority",
                     ]))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Translation::class), $this->identicalTo('t'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('t.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(4))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('t.type IN (:types)')],
                         [$this->identicalTo('t.locale IN (:localePrimary, :localeSecondary)')],
                         [$this->identicalTo("{$searchField} LIKE :keyword0")],
                         [$this->identicalTo("{$searchField} LIKE :keyword1")]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('addGroupBy')
                     ->withConsecutive(
                         [$this->identicalTo('t.type')],
                         [$this->identicalTo('t.name')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(8))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('localePrimary'),
                             $this->identicalTo($locale)
                         ],
                         [
                             $this->identicalTo('localeSecondary'),
                             $this->identicalTo('en')
                         ],
                         [
                             $this->identicalTo('priorityPrimary'),
                             $this->identicalTo(SearchResultPriority::PRIMARY_LOCALE_MATCH)
                         ],
                         [
                             $this->identicalTo('prioritySecondary'),
                             $this->identicalTo(SearchResultPriority::SECONDARY_LOCALE_MATCH)
                         ],
                         [
                             $this->identicalTo('types'),
                             $this->identicalTo([EntityType::ITEM, EntityType::FLUID, EntityType::RECIPE])
                         ],
                         [
                             $this->identicalTo('keyword0'),
                             $this->identicalTo('%foo%')
                         ],
                         [
                             $this->identicalTo('keyword1'),
                             $this->identicalTo('%b\\_a\\\\r\\%%')
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['mapTranslationPriorityDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('mapTranslationPriorityDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $repository->findDataByKeywords($combinationId, $locale, $keywords);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * Tests the findDataByKeywords method.
     * @covers ::findDataByKeywords
     */
    public function testFindDataByKeywordsWithoutKeywords(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['mapTranslationPriorityDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapTranslationPriorityDataResult');

        $result = $repository->findDataByKeywords($combinationId, $locale, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the mapTranslationPriorityDataResult method.
     * @throws ReflectionException
     * @covers ::mapTranslationPriorityDataResult
     */
    public function testMapTranslationPriorityDataResult(): void
    {
        $translationPriorityData = [
            [
                'type' => 'abc',
                'name' => 'def',
                'priority' => '42',
            ],
            [
                'type' => 'ghi',
                'name' => 'jkl',
                'priority' => '21',
            ],
        ];

        $data1 = new TranslationPriorityData();
        $data1->setType('abc')
              ->setName('def')
              ->setPriority(42);
        $data2 = new TranslationPriorityData();
        $data2->setType('ghi')
              ->setName('jkl')
              ->setPriority(21);
        $expectedResult = [$data1, $data2];

        $repository = new TranslationRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'mapTranslationPriorityDataResult', $translationPriorityData);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the clearCrossTable method.
     * @throws ReflectionException
     * @covers ::clearCrossTable
     */
    public function testClearCrossTable(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        $combinationId->expects($this->once())
                      ->method('getBytes')
                      ->willReturn('abc');

        $expectedQuery = 'DELETE FROM `CombinationXTranslation` WHERE `combinationId` = ?';
        $expectedParameters = ['abc'];

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['executeNativeSql'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('executeNativeSql')
                   ->with($this->identicalTo($expectedQuery), $this->identicalTo($expectedParameters));

        $repository->clearCrossTable($combinationId);
    }

    /**
     * Tests the persistTranslationsToCombination method.
     * @covers ::persistTranslationsToCombination
     * @throws DBALException
     */
    public function testPersistTranslationsToCombination(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        $translations = [
            $this->createMock(Translation::class),
            $this->createMock(Translation::class),
        ];

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['insertTranslations', 'clearCrossTable', 'insertIntoCrossTable'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('insertTranslations')
                   ->with($this->identicalTo($translations));
        $repository->expects($this->once())
                   ->method('insertIntoCrossTable')
                   ->with($this->identicalTo($combinationId), $this->identicalTo($translations));

        $repository->persistTranslationsToCombination($combinationId, $translations);
    }

    /**
     * Tests the insertTranslations method.
     * @throws ReflectionException
     * @covers ::insertTranslations
     */
    public function testInsertTranslations(): void
    {
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        $id1->expects($this->once())
            ->method('getBytes')
            ->willReturn('abc');
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);
        $id2->expects($this->once())
            ->method('getBytes')
            ->willReturn('def');

        $translation1 = new Translation();
        $translation1->setId($id1)
                     ->setLocale('ghi')
                     ->setType('jkl')
                     ->setName('mno')
                     ->setValue('pqr')
                     ->setDescription('stu')
                     ->setIsDuplicatedByMachine(true)
                     ->setIsDuplicatedByRecipe(false);

        $translation2 = new Translation();
        $translation2->setId($id2)
                     ->setLocale('vwx')
                     ->setType('yza')
                     ->setName('bcd')
                     ->setValue('efg')
                     ->setDescription('hij')
                     ->setIsDuplicatedByMachine(false)
                     ->setIsDuplicatedByRecipe(true);

        $expectedParameters = [
            'abc', 'ghi', 'jkl', 'mno', 'pqr', 'stu', true, false,
            'def', 'vwx', 'yza', 'bcd', 'efg', 'hij', false, true,
        ];
        $placeholders = 'klm';

        $expectedQuery = 'INSERT IGNORE INTO `Translation` '
            . '(`id`,`locale`,`type`,`name`,`value`,`description`,`isDuplicatedByMachine`,`isDuplicatedByRecipe`) '
            . 'VALUES klm';

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['buildParameterPlaceholders', 'executeNativeSql'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('buildParameterPlaceholders')
                   ->with($this->identicalTo(2), $this->identicalTo(8))
                   ->willReturn($placeholders);
        $repository->expects($this->once())
                   ->method('executeNativeSql')
                   ->with($this->identicalTo($expectedQuery), $this->identicalTo($expectedParameters));

        $this->invokeMethod($repository, 'insertTranslations', [$translation1, $translation2]);
    }

    /**
     * Tests the insertTranslations method.
     * @throws ReflectionException
     * @covers ::insertTranslations
     */
    public function testInsertTranslationsWithoutTranslations(): void
    {
        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['buildParameterPlaceholders', 'executeNativeSql'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('buildParameterPlaceholders');
        $repository->expects($this->never())
                   ->method('executeNativeSql');

        $this->invokeMethod($repository, 'insertTranslations', []);
    }

    /**
     * Tests the insertIntoCrossTable method.
     * @throws ReflectionException
     * @covers ::insertIntoCrossTable
     */
    public function testInsertIntoCrossTable(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        $combinationId->expects($this->atLeastOnce())
                      ->method('getBytes')
                      ->willReturn('abc');

        /* @var UuidInterface&MockObject $translationId1 */
        $translationId1 = $this->createMock(UuidInterface::class);
        $translationId1->expects($this->once())
                       ->method('getBytes')
                       ->willReturn('def');
        /* @var UuidInterface&MockObject $translationId2 */
        $translationId2 = $this->createMock(UuidInterface::class);
        $translationId2->expects($this->once())
                       ->method('getBytes')
                       ->willReturn('ghi');

        $translation1 = new Translation();
        $translation1->setId($translationId1);
        $translation2 = new Translation();
        $translation2->setId($translationId2);

        $placeholders = 'jkl';
        $expectedQuery = 'INSERT INTO `CombinationXTranslation` (`combinationId`, `translationId`) '
            . 'VALUES jkl';
        $expectedParameters = ['abc', 'def', 'abc', 'ghi'];

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['buildParameterPlaceholders', 'executeNativeSql'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('buildParameterPlaceholders')
                   ->with($this->identicalTo(2), $this->identicalTo(2))
                   ->willReturn($placeholders);
        $repository->expects($this->once())
                   ->method('executeNativeSql')
                   ->with($this->identicalTo($expectedQuery), $this->identicalTo($expectedParameters));

        $this->invokeMethod($repository, 'insertIntoCrossTable', $combinationId, [$translation1, $translation2]);
    }

    /**
     * Tests the insertIntoCrossTable method.
     * @throws ReflectionException
     * @covers ::insertIntoCrossTable
     */
    public function testInsertIntoCrossTableWithoutTranslations(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        /* @var TranslationRepository&MockObject $repository */
        $repository = $this->getMockBuilder(TranslationRepository::class)
                           ->onlyMethods(['buildParameterPlaceholders', 'executeNativeSql'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('buildParameterPlaceholders');
        $repository->expects($this->never())
                   ->method('executeNativeSql');

        $this->invokeMethod($repository, 'insertIntoCrossTable', $combinationId, []);
    }

    /**
     * Tests the buildParameterPlaceholders method.
     * @throws ReflectionException
     * @covers ::buildParameterPlaceholders
     */
    public function testBuildParameterPlaceholders(): void
    {
        $numberOfRows = 3;
        $numberOfValues = 4;
        $expectedResult = '(?,?,?,?),(?,?,?,?),(?,?,?,?)';

        $repository = new TranslationRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'buildParameterPlaceholders', $numberOfRows, $numberOfValues);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the executeNativeSql method.
     * @throws ReflectionException
     * @covers ::executeNativeSql
     */
    public function testExecuteNativeSql(): void
    {
        $query = 'abc';
        $parameters = ['def', 'ghi'];

        /* @var Statement&MockObject $statement */
        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
                  ->method('execute')
                  ->with($this->identicalTo($parameters));

        /* @var Connection&MockObject $connection */
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
                   ->method('prepare')
                   ->with($this->identicalTo($query))
                   ->willReturn($statement);

        $this->entityManager->expects($this->once())
                            ->method('getConnection')
                            ->willReturn($connection);

        $repository = new TranslationRepository($this->entityManager);
        $this->invokeMethod($repository, 'executeNativeSql', $query, $parameters);
    }
}
