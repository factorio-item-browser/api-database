<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Repository\RecipeRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the RecipeRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\RecipeRepository
 */
class RecipeRepositoryTest extends TestCase
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
     * @return RecipeRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): RecipeRepository
    {
        return $this->getMockBuilder(RecipeRepository::class)
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMock();
    }

    public function testFindByIds(): void
    {
        $ids = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $mappedIds = ['def', 'ghi'];
        $queryResult = [
            $this->createMock(Recipe::class),
            $this->createMock(Recipe::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with(
                         $this->identicalTo('r'),
                         $this->identicalTo('ri'),
                         $this->identicalTo('rii'),
                         $this->identicalTo('rp'),
                         $this->identicalTo('rpi')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Recipe::class), $this->identicalTo('r'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(4))
                     ->method('leftJoin')
                     ->withConsecutive(
                         [$this->identicalTo('r.ingredients'), $this->identicalTo('ri')],
                         [$this->identicalTo('ri.item'), $this->identicalTo('rii')],
                         [$this->identicalTo('r.products'), $this->identicalTo('rp')],
                         [$this->identicalTo('rp.item'), $this->identicalTo('rpi')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('r.id IN (:ids)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('ids'),
                         $this->identicalTo($mappedIds)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance(['mapIdsToParameterValues']);
        $instance->expects($this->once())
                 ->method('mapIdsToParameterValues')
                 ->with($this->identicalTo($ids))
                 ->willReturn($mappedIds);

        $result = $instance->findByIds($ids);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByIdsWithoutIds(): void
    {
        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance(['getEntityClass', 'mapIdsToParameterValues']);
        $instance->expects($this->never())
                 ->method('mapIdsToParameterValues');

        $result = $instance->findByIds([]);

        $this->assertSame([], $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEntityClass(): void
    {
        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'getEntityClass');

        $this->assertSame(Recipe::class, $result);
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

    /**
     * @throws ReflectionException
     */
    public function testRemoveIds(): void
    {
        $ids = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $mappedIds = ['abc', 'def'];

        $query1 = $this->createMock(AbstractQuery::class);
        $query1->expects($this->once())
               ->method('execute');

        $query2 = $this->createMock(AbstractQuery::class);
        $query2->expects($this->once())
               ->method('execute');

        $query3 = $this->createMock(AbstractQuery::class);
        $query3->expects($this->once())
               ->method('execute');

        $queryBuilder1 = $this->createMock(QueryBuilder::class);
        $queryBuilder1->expects($this->once())
                      ->method('delete')
                      ->with($this->identicalTo(RecipeIngredient::class), $this->identicalTo('e'))
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('andWhere')
                      ->with($this->identicalTo('e.recipe IN (:ids)'))
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('setParameter')
                      ->with($this->identicalTo('ids'), $this->identicalTo($mappedIds))
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query1);

        $queryBuilder2 = $this->createMock(QueryBuilder::class);
        $queryBuilder2->expects($this->once())
                      ->method('delete')
                      ->with($this->identicalTo(RecipeProduct::class), $this->identicalTo('e'))
                      ->willReturnSelf();
        $queryBuilder2->expects($this->once())
                      ->method('andWhere')
                      ->with($this->identicalTo('e.recipe IN (:ids)'))
                      ->willReturnSelf();
        $queryBuilder2->expects($this->once())
                      ->method('setParameter')
                      ->with($this->identicalTo('ids'), $this->identicalTo($mappedIds))
                      ->willReturnSelf();
        $queryBuilder2->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query2);

        $queryBuilder3 = $this->createMock(QueryBuilder::class);
        $queryBuilder3->expects($this->once())
                      ->method('delete')
                      ->with($this->identicalTo(Recipe::class), $this->identicalTo('e'))
                      ->willReturnSelf();
        $queryBuilder3->expects($this->once())
                      ->method('andWhere')
                      ->with($this->identicalTo('e.id IN (:ids)'))
                      ->willReturnSelf();
        $queryBuilder3->expects($this->once())
                      ->method('setParameter')
                      ->with($this->identicalTo('ids'), $this->identicalTo($mappedIds))
                      ->willReturnSelf();
        $queryBuilder3->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query3);

        $this->entityManager->expects($this->exactly(3))
                            ->method('createQueryBuilder')
                            ->willReturnOnConsecutiveCalls(
                                $queryBuilder1,
                                $queryBuilder2,
                                $queryBuilder3
                            );

        $instance = $this->createInstance(['mapIdsToParameterValues']);
        $instance->expects($this->exactly(3))
                 ->method('mapIdsToParameterValues')
                 ->with($this->identicalTo($ids))
                 ->willReturn($mappedIds);

        $this->invokeMethod($instance, 'removeIds', $ids);
    }

    public function testFindDataByNames(): void
    {
        $names = ['abc', 'def'];
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            ['id' => Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef')],
            ['id' => Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210')],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                     ]))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Recipe::class), $this->identicalTo('r'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('r.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('r.name IN (:names)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('names'),
                             $this->identicalTo($names)
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance(['mapRecipeDataResult']);
        $instance->expects($this->once())
                   ->method('mapRecipeDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $instance->findDataByNames($combinationId, $names);

        $this->assertSame($mappedResult, $result);
    }

    public function testFindDataByNamesWithoutNames(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance(['mapRecipeDataResult']);
        $instance->expects($this->never())
                 ->method('mapRecipeDataResult');

        $result = $instance->findDataByNames($combinationId, []);

        $this->assertSame([], $result);
    }

    public function testFindDataByIngredientItemIds(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $itemIds = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $data = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];
        $sortedData = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        $instance = $this->createInstance(['findDataByItemIds', 'orderByNumberOfItems']);
        $instance->expects($this->once())
                 ->method('findDataByItemIds')
                 ->with(
                     $this->identicalTo($combinationId),
                     $this->identicalTo('ingredients'),
                     $this->identicalTo($itemIds)
                 )
                 ->willReturn($data);
        $instance->expects($this->once())
                 ->method('orderByNumberOfItems')
                 ->with($this->identicalTo(RecipeIngredient::class), $this->identicalTo($data))
                 ->willReturn($sortedData);

        $result = $instance->findDataByIngredientItemIds($combinationId, $itemIds);

        $this->assertSame($sortedData, $result);
    }

    public function testFindDataByProductItemIds(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $itemIds = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $data = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];
        $sortedData = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        $instance = $this->createInstance(['findDataByItemIds', 'orderByNumberOfItems']);
        $instance->expects($this->once())
                 ->method('findDataByItemIds')
                 ->with(
                     $this->identicalTo($combinationId),
                     $this->identicalTo('products'),
                     $this->identicalTo($itemIds)
                 )
                 ->willReturn($data);
        $instance->expects($this->once())
                 ->method('orderByNumberOfItems')
                 ->with($this->identicalTo(RecipeProduct::class), $this->identicalTo($data))
                 ->willReturn($sortedData);

        $result = $instance->findDataByProductItemIds($combinationId, $itemIds);

        $this->assertSame($sortedData, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testFindDataByItemIds(): void
    {
        $recipeProperty = 'abc';
        $itemIds = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $mappedItemIds = ['def', 'ghi'];
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            ['id' => Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef')],
            ['id' => Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210')],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                         'IDENTITY(i.item) AS itemId',
                     ]))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Recipe::class), $this->identicalTo('r'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('innerJoin')
                     ->withConsecutive(
                         [
                             $this->identicalTo('r.combinations'),
                             $this->identicalTo('c'),
                             $this->identicalTo('WITH'),
                             $this->identicalTo('c.id = :combinationId'),
                         ],
                         [
                             $this->identicalTo('r.abc'),
                             $this->identicalTo('i'),
                             $this->identicalTo('WITH'),
                             $this->identicalTo('i.item IN (:itemIds)'),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('itemIds'),
                             $this->identicalTo($mappedItemIds)
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('addOrderBy')
                     ->withConsecutive(
                         [$this->identicalTo('r.name'), $this->identicalTo('ASC')],
                         [$this->identicalTo('r.mode'), $this->identicalTo('ASC')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance(['mapIdsToParameterValues', 'mapRecipeDataResult']);
        $instance->expects($this->once())
                 ->method('mapIdsToParameterValues')
                 ->with($this->identicalTo($itemIds))
                 ->willReturn($mappedItemIds);
        $instance->expects($this->once())
                 ->method('mapRecipeDataResult')
                 ->with($this->identicalTo($queryResult))
                 ->willReturn($mappedResult);

        $result = $this->invokeMethod($instance, 'findDataByItemIds', $combinationId, $recipeProperty, $itemIds);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testFindDataByItemIdsWithoutItemIds(): void
    {
        $recipeProperty = 'abc';
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance(['mapIdsToParameterValues', 'mapRecipeDataResult']);
        $instance->expects($this->never())
                 ->method('mapIdsToParameterValues');
        $instance->expects($this->never())
                 ->method('mapRecipeDataResult');

        $result = $this->invokeMethod($instance, 'findDataByItemIds', $combinationId, $recipeProperty, []);

        $this->assertSame([], $result);
    }

    public function testFindDataByKeywords(): void
    {
        $keywords = ['foo', 'b_a\\r%'];
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            ['id' => Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef')],
            ['id' => Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210')],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                     ]))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Recipe::class), $this->identicalTo('r'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('r.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('r.name LIKE :keyword0')],
                         [$this->identicalTo('r.name LIKE :keyword1')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
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

        $instance = $this->createInstance(['mapRecipeDataResult']);
        $instance->expects($this->once())
                   ->method('mapRecipeDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $instance->findDataByKeywords($combinationId, $keywords);

        $this->assertSame($mappedResult, $result);
    }

    public function testFindDataByKeywordsWithoutKeywords(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance(['mapRecipeDataResult']);
        $instance->expects($this->never())
                 ->method('mapRecipeDataResult');

        $result = $instance->findDataByKeywords($combinationId, []);

        $this->assertSame([], $result);
    }

    public function testFindAllData(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            ['id' => Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef')],
            ['id' => Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210')],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                     ]))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Recipe::class), $this->identicalTo('r'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('r.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('combinationId'),
                         $this->identicalTo($combinationId),
                         $this->identicalTo(UuidBinaryType::NAME)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('addOrderBy')
                     ->with($this->identicalTo('r.name'), $this->identicalTo('ASC'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance(['mapRecipeDataResult']);
        $instance->expects($this->once())
                 ->method('mapRecipeDataResult')
                 ->with($this->identicalTo($queryResult))
                 ->willReturn($mappedResult);

        $result = $instance->findAllData($combinationId);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testMapRecipeDataResult(): void
    {
        $id1 = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $id2 = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');
        $itemId = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $machineData = [
            [
                'id' => $id1,
                'name' => 'abc',
                'mode' => 'def',
            ],
            [
                'id' => $id2,
                'name' => 'ghi',
                'mode' => 'jkl',
                'itemId' => $itemId->getBytes(),
            ],
        ];

        $data1 = new RecipeData();
        $data1->setId($id1)
              ->setName('abc')
              ->setMode('def');
        $data2 = new RecipeData();
        $data2->setId($id2)
              ->setName('ghi')
              ->setMode('jkl')
              ->setItemId($itemId);
        $expectedResult = [$data1, $data2];

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'mapRecipeDataResult', $machineData);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testOrderByNumberOfItems(): void
    {
        $recipeId1 = Uuid::fromString('1fbe59ad-04a3-4654-b939-392c450fe222');
        $recipeId2 = Uuid::fromString('22ca55c7-bb83-4ba1-b2e7-98bfe66948f5');
        $recipeId3 = Uuid::fromString('37462be0-ae18-4689-b393-ae76ff91b056');
        $recipeId4 = Uuid::fromString('45f51893-0676-47ad-b416-26a9d67925a5');

        $data1 = new RecipeData();
        $data1->setId($recipeId1);
        $data2 = new RecipeData();
        $data2->setId($recipeId2);
        $data3 = new RecipeData();
        $data3->setId($recipeId3);
        $data4 = new RecipeData();
        $data4->setId($recipeId4);
        $data = [$data1, $data2, $data3, $data4];

        $mappedRecipeIds = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $expectedRecipeIds = [$recipeId1, $recipeId2, $recipeId3, $recipeId4];

        $queryResult = [
            [
                'recipeId' => $recipeId1->getBytes(),
                'number' => '2',
            ],
            [
                'recipeId' => $recipeId3->getBytes(),
                'number' => '7',
            ],
            [
                'recipeId' => $recipeId4->getBytes(),
                'number' => '2',
            ],
        ];
        $expectedResult = [
            $data2,
            $data1,
            $data4,
            $data3,
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo(['IDENTITY(i.recipe) AS recipeId', 'MAX(i.order) AS number']))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(RecipeProduct::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('i.recipe IN (:recipeIds)')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('addGroupBy')
                     ->with($this->identicalTo('i.recipe'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with($this->identicalTo('recipeIds'), $this->identicalTo($mappedRecipeIds))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance(['mapIdsToParameterValues']);
        $instance->expects($this->once())
                 ->method('mapIdsToParameterValues')
                 ->with($this->identicalTo($expectedRecipeIds))
                 ->willReturn($mappedRecipeIds);

        $result = $this->invokeMethod($instance, 'orderByNumberOfItems', RecipeProduct::class, $data);

        $this->assertSame($expectedResult, $result);
    }
}
