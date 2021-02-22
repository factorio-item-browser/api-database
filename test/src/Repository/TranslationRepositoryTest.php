<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception as DBALException;
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
 * @covers \FactorioItemBrowser\Api\Database\Repository\TranslationRepository
 */
class TranslationRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return TranslationRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): TranslationRepository
    {
        return $this->getMockBuilder(TranslationRepository::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMock();
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEntityClass(): void
    {
        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'getEntityClass');

        $this->assertSame(Translation::class, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with($this->identicalTo('abc.combinations'), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id IS NULL'))
                     ->willReturnSelf();

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'addOrphanConditions', $queryBuilder, $alias);
    }

    public function testFindByTypesAndNames(): void
    {
        $locale = 'abc';

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

        $expectedCondition = '((t.type IN (:types0) AND t.name IN (:names0))'
            . ' OR (t.type IN (:types1) AND t.name IN (:names1))'
            . ' OR (t.type IN (:types2) AND t.name IN (:names2)))';

        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Translation::class),
            $this->createMock(Translation::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

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
                             $this->identicalTo(UuidBinaryType::NAME),
                         ],
                         [
                             $this->identicalTo('locales'),
                             $this->identicalTo([$locale, 'en']),
                         ],
                         [
                             $this->identicalTo('types0'),
                             $this->equalTo([EntityType::RECIPE, EntityType::FLUID, EntityType::ITEM]),
                         ],
                         [
                             $this->identicalTo('names0'),
                             $this->identicalTo(['def', 'ghi'])
                         ],
                         [
                             $this->identicalTo('types1'),
                             $this->equalTo([EntityType::MACHINE, EntityType::FLUID, EntityType::ITEM]),
                         ],
                         [
                             $this->identicalTo('names1'),
                             $this->identicalTo(['jkl']),
                         ],
                         [
                             $this->identicalTo('types2'),
                             $this->equalTo([EntityType::ITEM]),
                         ],
                         [
                             $this->identicalTo('names2'),
                             $this->identicalTo(['mno', 'pqr']),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance();
        $result = $instance->findByTypesAndNames($combinationId, $locale, $namesByTypes);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByTypesAndNamesWithoutConditions(): void
    {
        $locale = 'abc';

        $namesByTypes = $this->createMock(NamesByTypes::class);
        $namesByTypes->expects($this->once())
                     ->method('isEmpty')
                     ->willReturn(true);

        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance();
        $result = $instance->findByTypesAndNames($combinationId, $locale, $namesByTypes);

        $this->assertSame([], $result);
    }

    public function testFindDataByKeywords(): void
    {
        $locale = 'abc';
        $keywords = ['foo', 'b_a\\r%'];
        $priority = 'CASE WHEN t.locale = :localePrimary THEN :priorityPrimary ELSE :prioritySecondary END';
        $searchField = "LOWER(CONCAT(t.value, '|', t.description))";

        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $this->createMock(UuidInterface::class)],
            ['id' => $this->createMock(UuidInterface::class)],
        ];
        $mappedResult = [
            $this->createMock(TranslationPriorityData::class),
            $this->createMock(TranslationPriorityData::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

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

        $instance = $this->createInstance(['mapTranslationPriorityDataResult']);
        $instance->expects($this->once())
                 ->method('mapTranslationPriorityDataResult')
                 ->with($this->identicalTo($queryResult))
                 ->willReturn($mappedResult);

        $result = $instance->findDataByKeywords($combinationId, $locale, $keywords);

        $this->assertSame($mappedResult, $result);
    }

    public function testFindDataByKeywordsWithoutKeywords(): void
    {
        $locale = 'abc';
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance(['mapTranslationPriorityDataResult']);
        $instance->expects($this->never())
                 ->method('mapTranslationPriorityDataResult');

        $result = $instance->findDataByKeywords($combinationId, $locale, []);

        $this->assertSame([], $result);
    }

    /**
     * @throws ReflectionException
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

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'mapTranslationPriorityDataResult', $translationPriorityData);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @throws DBALException
     * @throws DriverException
     */
    public function testClearCrossTable(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $combinationId->expects($this->once())
                      ->method('getBytes')
                      ->willReturn('abc');

        $expectedQuery = 'DELETE FROM `CombinationXTranslation` WHERE `combinationId` = ?';
        $expectedParameters = ['abc'];

        $instance = $this->createInstance(['executeNativeSql']);
        $instance->expects($this->once())
                 ->method('executeNativeSql')
                 ->with($this->identicalTo($expectedQuery), $this->identicalTo($expectedParameters));

        $instance->clearCrossTable($combinationId);
    }

    /**
     * @throws DBALException
     * @throws DriverException
     */
    public function testPersistTranslationsToCombination(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $translations = [
            $this->createMock(Translation::class),
            $this->createMock(Translation::class),
        ];

        $instance = $this->createInstance(['insertTranslations', 'clearCrossTable', 'insertIntoCrossTable']);
        $instance->expects($this->once())
                 ->method('insertTranslations')
                 ->with($this->identicalTo($translations));
        $instance->expects($this->once())
                 ->method('insertIntoCrossTable')
                 ->with($this->identicalTo($combinationId), $this->identicalTo($translations));

        $instance->persistTranslationsToCombination($combinationId, $translations);
    }

    /**
     * @throws ReflectionException
     */
    public function testInsertTranslations(): void
    {
        $id1 = $this->createMock(UuidInterface::class);
        $id1->expects($this->once())
            ->method('getBytes')
            ->willReturn('abc');
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

        $instance = $this->createInstance(['buildParameterPlaceholders', 'executeNativeSql']);
        $instance->expects($this->once())
                 ->method('buildParameterPlaceholders')
                 ->with($this->identicalTo(2), $this->identicalTo(8))
                 ->willReturn($placeholders);
        $instance->expects($this->once())
                 ->method('executeNativeSql')
                 ->with($this->identicalTo($expectedQuery), $this->identicalTo($expectedParameters));

        $this->invokeMethod($instance, 'insertTranslations', [$translation1, $translation2]);
    }

    /**
     * @throws ReflectionException
     */
    public function testInsertTranslationsWithoutTranslations(): void
    {
        $instance = $this->createInstance(['buildParameterPlaceholders', 'executeNativeSql']);
        $instance->expects($this->never())
                 ->method('buildParameterPlaceholders');
        $instance->expects($this->never())
                 ->method('executeNativeSql');

        $this->invokeMethod($instance, 'insertTranslations', []);
    }

    /**
     * @throws ReflectionException
     */
    public function testInsertIntoCrossTable(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $combinationId->expects($this->atLeastOnce())
                      ->method('getBytes')
                      ->willReturn('abc');

        $translationId1 = $this->createMock(UuidInterface::class);
        $translationId1->expects($this->once())
                       ->method('getBytes')
                       ->willReturn('def');
        $translationId2 = $this->createMock(UuidInterface::class);
        $translationId2->expects($this->once())
                       ->method('getBytes')
                       ->willReturn('ghi');

        $translation1 = new Translation();
        $translation1->setId($translationId1);
        $translation2 = new Translation();
        $translation2->setId($translationId2);

        $placeholders = 'jkl';
        $expectedQuery = 'INSERT IGNORE INTO `CombinationXTranslation` (`combinationId`, `translationId`) VALUES jkl';
        $expectedParameters = ['abc', 'def', 'abc', 'ghi'];

        $instance = $this->createInstance(['buildParameterPlaceholders', 'executeNativeSql']);
        $instance->expects($this->once())
                 ->method('buildParameterPlaceholders')
                 ->with($this->identicalTo(2), $this->identicalTo(2))
                 ->willReturn($placeholders);
        $instance->expects($this->once())
                 ->method('executeNativeSql')
                 ->with($this->identicalTo($expectedQuery), $this->identicalTo($expectedParameters));

        $this->invokeMethod($instance, 'insertIntoCrossTable', $combinationId, [$translation1, $translation2]);
    }

    /**
     * @throws ReflectionException
     */
    public function testInsertIntoCrossTableWithoutTranslations(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);

        $instance = $this->createInstance(['buildParameterPlaceholders', 'executeNativeSql']);
        $instance->expects($this->never())
                 ->method('buildParameterPlaceholders');
        $instance->expects($this->never())
                 ->method('executeNativeSql');

        $this->invokeMethod($instance, 'insertIntoCrossTable', $combinationId, []);
    }

    /**
     * @throws ReflectionException
     */
    public function testBuildParameterPlaceholders(): void
    {
        $numberOfRows = 3;
        $numberOfValues = 4;
        $expectedResult = '(?,?,?,?),(?,?,?,?),(?,?,?,?)';

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'buildParameterPlaceholders', $numberOfRows, $numberOfValues);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testExecuteNativeSql(): void
    {
        $query = 'abc';
        $parameters = ['def', 'ghi'];

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())
                  ->method('execute')
                  ->with($this->identicalTo($parameters));

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
                   ->method('prepare')
                   ->with($this->identicalTo($query))
                   ->willReturn($statement);

        $this->entityManager->expects($this->once())
                            ->method('getConnection')
                            ->willReturn($connection);

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'executeNativeSql', $query, $parameters);
    }
}
