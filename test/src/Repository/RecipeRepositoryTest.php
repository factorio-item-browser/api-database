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
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the RecipeRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\RecipeRepository
 */
class RecipeRepositoryTest extends TestCase
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
        $repository = new RecipeRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(Recipe::class, $result);
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

        $repository = new RecipeRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }

    /**
     * Tests the removeIds method.
     * @throws ReflectionException
     * @covers ::removeIds
     */
    public function testRemoveIds(): void
    {
        $ids = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $mappedIds = ['abc', 'def'];

        /* @var AbstractQuery&MockObject $query1 */
        $query1 = $this->createMock(AbstractQuery::class);
        $query1->expects($this->once())
               ->method('execute');

        /* @var AbstractQuery&MockObject $query2 */
        $query2 = $this->createMock(AbstractQuery::class);
        $query2->expects($this->once())
               ->method('execute');
        
        /* @var AbstractQuery&MockObject $query3 */
        $query3 = $this->createMock(AbstractQuery::class);
        $query3->expects($this->once())
               ->method('execute');

        /* @var QueryBuilder&MockObject $queryBuilder */
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

        /* @var QueryBuilder&MockObject $queryBuilder */
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

        /* @var QueryBuilder&MockObject $queryBuilder */
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

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods([ 'mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->exactly(3))
                   ->method('mapIdsToParameterValues')
                   ->with($this->identicalTo($ids))
                   ->willReturn($mappedIds);

        $this->invokeMethod($repository, 'removeIds', $ids);
    }

    /**
     * Tests the findDataByNames method.
     * @covers ::findDataByNames
     */
    public function testFindDataByNames(): void
    {
        $names = ['abc', 'def'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $this->createMock(UuidInterface::class)],
            ['id' => $this->createMock(UuidInterface::class)],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
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

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('mapRecipeDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $repository->findDataByNames($combinationId, $names);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * Tests the findDataByNames method.
     * @covers ::findDataByNames
     */
    public function testFindDataByNamesWithoutNames(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapRecipeDataResult');

        $result = $repository->findDataByNames($combinationId, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findDataByIngredientItemIds method.
     * @covers ::findDataByIngredientItemIds
     */
    public function testFindDataByIngredientItemIds(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        
        $itemIds = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $data = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];
        
        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['findDataByItemIds'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('findDataByItemIds')
                   ->with(
                       $this->identicalTo($combinationId),
                       $this->identicalTo('ingredients'),
                       $this->identicalTo($itemIds)
                   )
                   ->willReturn($data);
        
        $result = $repository->findDataByIngredientItemIds($combinationId, $itemIds);
        
        $this->assertSame($data, $result);
    }
    
    /**
     * Tests the findDataByProductItemIds method.
     * @covers ::findDataByProductItemIds
     */
    public function testFindDataByProductItemIds(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        
        $itemIds = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $data = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
        ];
        
        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['findDataByItemIds'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('findDataByItemIds')
                   ->with(
                       $this->identicalTo($combinationId),
                       $this->identicalTo('products'),
                       $this->identicalTo($itemIds)
                   )
                   ->willReturn($data);
        
        $result = $repository->findDataByProductItemIds($combinationId, $itemIds);
        
        $this->assertSame($data, $result);
    }

    /**
     * Tests the findDataByItemIds method.
     * @throws ReflectionException
     * @covers ::findDataByItemIds
     */
    public function testFindDataByItemIds(): void
    {
        $recipeProperty = 'abc';
        $itemIds = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $mappedItemIds = ['def', 'ghi'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $this->createMock(UuidInterface::class)],
            ['id' => $this->createMock(UuidInterface::class)],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
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

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['mapIdsToParameterValues', 'mapRecipeDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('mapIdsToParameterValues')
                   ->with($this->identicalTo($itemIds))
                   ->willReturn($mappedItemIds);
        $repository->expects($this->once())
                   ->method('mapRecipeDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $this->invokeMethod($repository, 'findDataByItemIds', $combinationId, $recipeProperty, $itemIds);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * Tests the findDataByItemIds method.
     * @throws ReflectionException
     * @covers ::findDataByItemIds
     */
    public function testFindDataByItemIdsWithoutItemIds(): void
    {
        $recipeProperty = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['mapIdsToParameterValues', 'mapRecipeDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapIdsToParameterValues');
        $repository->expects($this->never())
                   ->method('mapRecipeDataResult');

        $result = $this->invokeMethod($repository, 'findDataByItemIds', $combinationId, $recipeProperty, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findDataByKeywords method.
     * @covers ::findDataByKeywords
     */
    public function testFindDataByKeywords(): void
    {
        $keywords = ['foo', 'b_a\\r%'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $this->createMock(UuidInterface::class)],
            ['id' => $this->createMock(UuidInterface::class)],
        ];
        $mappedResult = [
            $this->createMock(RecipeData::class),
            $this->createMock(RecipeData::class),
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

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('mapRecipeDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $repository->findDataByKeywords($combinationId, $keywords);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * Tests the findDataByKeywords method.
     * @covers ::findDataByKeywords
     */
    public function testFindDataByKeywordsWithoutKeywords(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var RecipeRepository&MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->onlyMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapRecipeDataResult');

        $result = $repository->findDataByKeywords($combinationId, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the mapRecipeDataResult method.
     * @throws ReflectionException
     * @covers ::mapRecipeDataResult
     */
    public function testMapRecipeDataResult(): void
    {
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);

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

        $repository = new RecipeRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'mapRecipeDataResult', $machineData);

        $this->assertEquals($expectedResult, $result);
    }
}
