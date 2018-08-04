<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\Common\Test\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ItemRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\ItemRepository
 */
class ItemRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Provides the data for the findByTypesAndNames test.
     * @return array
     */
    public function provideFindByTypesAndNames(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findByTypesAndNames method.
     * @param bool $withNamesByTypes
     * @param bool $withModCombinationIds
     * @covers ::findByTypesAndNames
     * @dataProvider provideFindByTypesAndNames
     */
    public function testFindByTypesAndNames(bool $withNamesByTypes, bool $withModCombinationIds)
    {
        $namesByTypes = $withNamesByTypes ? ['foo' => ['abc', 'def'], 'bar' => ['ghi']] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = [$this->createMock(Item::class)];
        $expectedResult = $withNamesByTypes ? $queryResult : [];

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
                             ->setMethods(['innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects(($withNamesByTypes && $withModCombinationIds) ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('i.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNamesByTypes ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['((i.type = :type0 AND i.name IN (:names0)) OR (i.type = :type1 AND i.name IN (:names1)))'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNamesByTypes ? $withModCombinationIds ? 5 : 4 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['type0', 'foo'],
                         ['names0', ['abc', 'def']],
                         ['type1', 'bar'],
                         ['names1', ['ghi']],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withNamesByTypes ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('i')
                   ->willReturn($queryBuilder);

        $result = $repository->findByTypesAndNames($namesByTypes, $modCombinationIds);
        $this->assertSame($expectedResult, $result);
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
     * @covers ::findByIds
     * @dataProvider provideFindByIds
     */
    public function testFindByIds(bool $withIds)
    {
        $ids = $withIds ? [42, 1337] : [];
        $queryResult = $withIds ? [$this->createMock(Item::class)] : [];

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
                             ->setMethods(['andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('i.id IN (:ids)')
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('ids', $ids)
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withIds ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('i')
                   ->willReturn($queryBuilder);

        $result = $repository->findByIds($ids);
        $this->assertSame($queryResult, $result);
    }
    
    /**
     * Provides the data for the findByKeywords test.
     * @return array
     */
    public function provideFindByKeywords(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findByKeywords method.
     * @param bool $withKeywords
     * @param bool $withModCombinationIds
     * @covers ::findByKeywords
     * @dataProvider provideFindByKeywords
     */
    public function testFindByKeywords(bool $withKeywords, bool $withModCombinationIds)
    {
        $keywords = $withKeywords ? ['foo', 'b_a\\r%'] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = [$this->createMock(Item::class)];
        $expectedResult = $withKeywords ? $queryResult : [];

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
                             ->setMethods(['andWhere', 'setParameter', 'innerJoin', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->exactly($withKeywords ? $withModCombinationIds ? 3 : 2 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['i.name LIKE :keyword0'],
                         ['i.name LIKE :keyword1'],
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
        $queryBuilder->expects(($withKeywords && $withModCombinationIds) ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('i.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($withKeywords ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withKeywords ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('i')
                   ->willReturn($queryBuilder);

        $result = $repository->findByKeywords($keywords, $modCombinationIds);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Provides the data for the findRandom test.
     * @return array
     */
    public function provideFindRandom(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findRandom method.
     * @param bool $withModCombinationIds
     * @covers ::findRandom
     * @dataProvider provideFindRandom
     */
    public function testFindRandom(bool $withModCombinationIds)
    {
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $numberOfItems = 21;
        $queryResult = [$this->createMock(Item::class)];

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
                             ->setMethods([
                                 'addSelect',
                                 'addOrderBy',
                                 'setMaxResults',
                                 'innerJoin',
                                 'andWhere',
                                 'setParameter',
                                 'getQuery'
                             ])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('addSelect')
                     ->with('RAND() AS HIDDEN rand')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('addOrderBy')
                     ->with('rand')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with($numberOfItems)
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('i.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('mc.id IN (:modCombinationIds)')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('modCombinationIds', $modCombinationIds)
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('i')
                   ->willReturn($queryBuilder);

        $result = $repository->findRandom($numberOfItems, $modCombinationIds);
        $this->assertEquals($queryResult, $result);
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
     * @covers ::removeOrphans
     * @dataProvider provideRemoveOrphans
     */
    public function testRemoveOrphans(array $orphanedIds, bool $expectRemove)
    {
        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['findOrphanedIds', 'removeIds'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('findOrphanedIds')
                   ->willReturn($orphanedIds);
        $repository->expects($expectRemove ? $this->once() : $this->never())
                   ->method('removeIds')
                   ->with($orphanedIds);

        $result = $repository->removeOrphans();
        $this->assertSame($repository, $result);
    }

    /**
     * Tests the findOrphanedIds method.
     * @throws ReflectionException
     * @covers ::findOrphanedIds
     */
    public function testFindOrphanedIds()
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
                             ->setMethods(['select', 'leftJoin', 'andWhere', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('i.id AS id')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('leftJoin')
                     ->withConsecutive(
                         ['i.modCombinations', 'mc'],
                         [RecipeIngredient::class, 'ri'],
                         [RecipeProduct::class, 'rp']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['mc.id IS NULL'],
                         ['ri.item IS NULL'],
                         ['rp.item IS NULL']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('i')
                   ->willReturn($queryBuilder);

        $result = $this->invokeMethod($repository, 'findOrphanedIds');
        $this->assertEquals($expectedResult, $result);
    }
    
    /**
     * Tests the removeIds method.
     * @throws ReflectionException
     * @covers ::removeIds
     */
    public function testRemoveIds()
    {
        $entityName = 'abc';
        $itemIds = [42, 1337];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['execute'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['delete', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($entityName, 'i')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('i.id IN (:itemIds)')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with('itemIds', $itemIds)
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ItemRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ItemRepository::class)
                           ->setMethods(['createQueryBuilder', 'getEntityName'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('i')
                   ->willReturn($queryBuilder);
        $repository->expects($this->once())
                   ->method('getEntityName')
                   ->willReturn($entityName);

        $result = $this->invokeMethod($repository, 'removeIds', $itemIds);
        $this->assertSame($repository, $result);
    }
}
