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
     * Provides the data for the findDataByNames test.
     * @return array
     */
    public function provideFindDataByNames(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByNames method.
     * @param bool $withNames
     * @param bool $withModCombinationIds
     * @throws ReflectionException
     * @covers ::findDataByNames
     * @dataProvider provideFindDataByNames
     */
    public function testFindDataByNames(bool $withNames, bool $withModCombinationIds): void
    {
        $names = $withNames ? ['abc', 'def'] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withNames ? [['id' => 42]] : [];
        $dataResult = $withNames ? [$this->createMock(RecipeData::class)] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withNames ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('select')
                     ->with([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                         'mc.order AS order'
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Recipe::class, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('r.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNames ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['r.name IN (:names)'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNames ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['names', $names],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withNames ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        /* @var RecipeRepository|MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->setMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withNames ? $this->once() : $this->never())
                   ->method('mapRecipeDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByNames($names, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }
    
    /**
     * Tests the findDataByIngredientItemIds method.
     * @throws ReflectionException
     * @covers ::findDataByIngredientItemIds
     */
    public function testFindDataByIngredientItemIds(): void
    {
        $itemIds = [13, 37];
        $modCombinationIds = [42, 1337];
        $dataResult = [$this->createMock(RecipeData::class)];

        /* @var RecipeRepository|MockObject $recipeRepository */
        $recipeRepository = $this->getMockBuilder(RecipeRepository::class)
                                 ->setMethods(['findDataByItemIds'])
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $recipeRepository->expects($this->once())
                         ->method('findDataByItemIds')
                         ->with('ingredients', $itemIds, $modCombinationIds)
                         ->willReturn($dataResult);

        $result = $recipeRepository->findDataByIngredientItemIds($itemIds, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Tests the findDataByProductItemIds method.
     * @throws ReflectionException
     * @covers ::findDataByProductItemIds
     */
    public function testFindDataByProductItemIds(): void
    {
        $itemIds = [13, 37];
        $modCombinationIds = [42, 1337];
        $dataResult = [$this->createMock(RecipeData::class)];

        /* @var RecipeRepository|MockObject $recipeRepository */
        $recipeRepository = $this->getMockBuilder(RecipeRepository::class)
                                 ->setMethods(['findDataByItemIds'])
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $recipeRepository->expects($this->once())
                         ->method('findDataByItemIds')
                         ->with('products', $itemIds, $modCombinationIds)
                         ->willReturn($dataResult);

        $result = $recipeRepository->findDataByProductItemIds($itemIds, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Provides the data for the findDataByItemIds test.
     * @return array
     */
    public function provideFindDataByItemIds(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByItemIds method.
     * @param bool $withItemIds
     * @param bool $withModCombinationIds
     * @throws ReflectionException
     * @covers ::findDataByItemIds
     * @dataProvider provideFindDataByItemIds
     */
    public function testFindDataByItemIds(bool $withItemIds, bool $withModCombinationIds): void
    {
        $recipeProperty = 'abc';
        $itemIds = $withItemIds ? [13, 37] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withItemIds ? [['id' => 42]] : [];
        $dataResult = $withItemIds ? [$this->createMock(RecipeData::class)] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withItemIds ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods([
                                 'select',
                                 'from',
                                 'innerJoin',
                                 'andWhere',
                                 'setParameter',
                                 'addOrderBy',
                                 'getQuery'
                             ])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withItemIds ? $this->once() : $this->never())
                     ->method('select')
                     ->with([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                         'IDENTITY(r2.item) AS itemId',
                         'mc.order AS order'
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($withItemIds ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Recipe::class, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($withItemIds ? $this->exactly(2) : $this->never())
                     ->method('innerJoin')
                     ->withConsecutive(
                         ['r.abc', 'r2'],
                         ['r.modCombinations', 'mc']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withItemIds ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['r2.item IN (:itemIds)'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withItemIds ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['itemIds', $itemIds],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withItemIds ? $this->exactly(2) : $this->never())
                     ->method('addOrderBy')
                     ->withConsecutive(
                         ['r.name', 'ASC'],
                         ['r.mode', 'ASC']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withItemIds ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withItemIds ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        /* @var RecipeRepository|MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->setMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withItemIds ? $this->once() : $this->never())
                   ->method('mapRecipeDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $this->invokeMethod($repository, 'findDataByItemIds', $recipeProperty, $itemIds, $modCombinationIds);
        $this->assertEquals($dataResult, $result);
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
     * @throws ReflectionException
     * @covers ::findDataByKeywords
     * @dataProvider provideFindDataByKeywords
     */
    public function testFindDataByKeywords(bool $withKeywords, bool $withModCombinationIds): void
    {
        $keywords = $withKeywords ? ['foo', 'b_a\\r%'] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withKeywords ? [['id' => 42]] : [];
        $dataResult = $withKeywords ? [$this->createMock(RecipeData::class)] : [];

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
                             ->setMethods(['select', 'from', 'andWhere', 'setParameter', 'innerJoin', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('select')
                     ->with([
                         'r.id AS id',
                         'r.name AS name',
                         'r.mode AS mode',
                         'mc.order AS order'
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Recipe::class, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('r.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withKeywords ? $withModCombinationIds ? 3 : 2 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['r.name LIKE :keyword0'],
                         ['r.name LIKE :keyword1'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withKeywords ? $withModCombinationIds ? 3 : 2 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['keyword0', '%foo%'],
                         ['keyword1', '%b\\_a\\\\r\\%%'],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withKeywords ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        /* @var RecipeRepository|MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->setMethods(['mapRecipeDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withKeywords ? $this->once() : $this->never())
                   ->method('mapRecipeDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByKeywords($keywords, $modCombinationIds);
        $this->assertEquals($dataResult, $result);
    }

    /**
     * Tests the mapRecipeDataResult method.
     * @throws ReflectionException
     * @covers ::mapRecipeDataResult
     */
    public function testMapRecipeDataResult(): void
    {
        $recipeData = [
            ['id' => 42],
            ['id' => 1337]
        ];
        $expectedResult = [
            (new RecipeData())->setId(42),
            (new RecipeData())->setId(1337),
        ];

        /* @var RecipeRepository $repository */
        $repository = $this->createMock(RecipeRepository::class);

        $result = $this->invokeMethod($repository, 'mapRecipeDataResult', $recipeData);
        $this->assertEquals($expectedResult, $result);
    }
    
    /**
     * Provides the data for the findByIds test.
     * @return array
     */
    public function provideFindByIds(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findByIds method.
     * @param bool $withIds
     * @throws ReflectionException
     * @covers ::findByIds
     * @dataProvider provideFindByIds
     */
    public function testFindByIds(bool $withIds): void
    {
        $ids = $withIds ? [42, 1337] : [];
        $queryResult = $withIds ? [$this->createMock(Recipe::class)] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withIds ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'leftJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('select')
                     ->with(['r', 'ri', 'rii', 'rp', 'rpi'])
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Recipe::class, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->exactly(4) : $this->never())
                     ->method('leftJoin')
                     ->withConsecutive(
                         ['r.ingredients', 'ri'],
                         ['ri.item', 'rii'],
                         ['r.products', 'rp'],
                         ['rp.item', 'rpi']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('r.id IN (:ids)')
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('ids', $ids)
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withIds ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new RecipeRepository($entityManager);

        $result = $repository->findByIds($ids);
        $this->assertSame($queryResult, $result);
    }
    
    
    /**
     * Provides the data for the removeOrphans test.
     * @return array
     */
    public function provideRemoveOrphans(): array
    {
        return [
            [[42, 1337], true],
            [[], false],
        ];
    }

    /**
     * Tests the removeOrphans method.
     * @param array $orphanedIds
     * @param bool $expectRemove
     * @throws ReflectionException
     * @covers ::removeOrphans
     * @dataProvider provideRemoveOrphans
     */
    public function testRemoveOrphans(array $orphanedIds, bool $expectRemove): void
    {
        /* @var RecipeRepository|MockObject $repository */
        $repository = $this->getMockBuilder(RecipeRepository::class)
                           ->setMethods(['findOrphanedIds', 'removeIds'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('findOrphanedIds')
                   ->willReturn($orphanedIds);
        $repository->expects($expectRemove ? $this->once() : $this->never())
                   ->method('removeIds')
                   ->with($orphanedIds);

        $repository->removeOrphans();
    }

    /**
     * Tests the findOrphanedIds method.
     * @throws ReflectionException
     * @covers ::findOrphanedIds
     */
    public function testFindOrphanedIds(): void
    {
        $queryResult = [
            ['id' => '42'],
            ['id' => '1337']
        ];
        $expectedResult = [42, 1337];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'leftJoin', 'andWhere', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('r.id AS id')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(Recipe::class, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with('r.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('mc.id IS NULL')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new RecipeRepository($entityManager);

        $result = $this->invokeMethod($repository, 'findOrphanedIds');
        $this->assertEquals($expectedResult, $result);
    }
    
    /**
     * Tests the removeIds method.
     * @throws ReflectionException
     * @covers ::removeIds
     */
    public function testRemoveIds(): void
    {
        $recipeIds = [42, 1337];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['execute'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($this->exactly(3))
              ->method('execute');

        /* @var QueryBuilder|MockObject $queryBuilder1 */
        $queryBuilder1 = $this->getMockBuilder(QueryBuilder::class)
                              ->setMethods(['delete', 'andWhere', 'setParameter', 'getQuery'])
                              ->disableOriginalConstructor()
                              ->getMock();
        $queryBuilder1->expects($this->once())
                      ->method('delete')
                      ->with(RecipeIngredient::class, 'ri')
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('andWhere')
                      ->with('ri.recipe IN (:recipeIds)')
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('setParameter')
                      ->with('recipeIds', $recipeIds)
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query);

        /* @var QueryBuilder|MockObject $queryBuilder2 */
        $queryBuilder2 = $this->getMockBuilder(QueryBuilder::class)
                              ->setMethods(['delete', 'andWhere', 'setParameter', 'getQuery'])
                              ->disableOriginalConstructor()
                              ->getMock();
        $queryBuilder2->expects($this->once())
                      ->method('delete')
                      ->with(RecipeProduct::class, 'rp')
                      ->willReturnSelf();
        $queryBuilder2->expects($this->once())
                      ->method('andWhere')
                      ->with('rp.recipe IN (:recipeIds)')
                      ->willReturnSelf();
        $queryBuilder2->expects($this->once())
                      ->method('setParameter')
                      ->with('recipeIds', $recipeIds)
                      ->willReturnSelf();
        $queryBuilder2->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query);


        /* @var QueryBuilder|MockObject $queryBuilder3 */
        $queryBuilder3 = $this->getMockBuilder(QueryBuilder::class)
                              ->setMethods(['delete', 'andWhere', 'setParameter', 'getQuery'])
                              ->disableOriginalConstructor()
                              ->getMock();
        $queryBuilder3->expects($this->once())
                      ->method('delete')
                      ->with(Recipe::class, 'r')
                      ->willReturnSelf();
        $queryBuilder3->expects($this->once())
                      ->method('andWhere')
                      ->with('r.id IN (:recipeIds)')
                      ->willReturnSelf();
        $queryBuilder3->expects($this->once())
                      ->method('setParameter')
                      ->with('recipeIds', $recipeIds)
                      ->willReturnSelf();
        $queryBuilder3->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($this->exactly(3))
                      ->method('createQueryBuilder')
                      ->willReturnOnConsecutiveCalls(
                          $queryBuilder1,
                          $queryBuilder2,
                          $queryBuilder3
                      );

        $repository = new RecipeRepository($entityManager);

        $this->invokeMethod($repository, 'removeIds', $recipeIds);
    }
}
