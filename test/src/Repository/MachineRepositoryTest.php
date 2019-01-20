<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\Common\Test\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\MachineData;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use FactorioItemBrowser\Api\Database\Repository\MachineRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the MachineRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\MachineRepository
 */
class MachineRepositoryTest extends TestCase
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
     * @covers ::findDataByNames
     * @dataProvider provideFindDataByNames
     */
    public function testFindDataByNames(bool $withNames, bool $withModCombinationIds): void
    {
        $names = $withNames ? ['abc', 'def'] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withNames ? [['id' => 42]] : [];
        $dataResult = $withNames ? [$this->createMock(MachineData::class)] : [];

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
                         'm.id AS id',
                         'm.name AS name',
                         'mc.order AS order'
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Machine::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('m.modCombinations', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withNames ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['m.name IN (:names)'],
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

        /* @var MachineRepository|MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->setMethods(['mapMachineDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withNames ? $this->once() : $this->never())
                   ->method('mapMachineDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByNames($names, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Provides the data for the findDataByCraftingCategories test.
     * @return array
     */
    public function provideFindDataByCraftingCategories(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByCraftingCategories method.
     * @param bool $withCraftingCategories
     * @param bool $withModCombinationIds
     * @covers ::findDataByCraftingCategories
     * @dataProvider provideFindDataByCraftingCategories
     */
    public function testFindDataByCraftingCategories(bool $withCraftingCategories, bool $withModCombinationIds): void
    {
        $craftingCategories = $withCraftingCategories ? ['abc', 'def'] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withCraftingCategories ? [['id' => 42]] : [];
        $dataResult = $withCraftingCategories ? [$this->createMock(MachineData::class)] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withCraftingCategories ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withCraftingCategories ? $this->once() : $this->never())
                     ->method('select')
                     ->with([
                         'm.id AS id',
                         'm.name AS name',
                         'mc.order AS order'
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($withCraftingCategories ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Machine::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withCraftingCategories ? $this->exactly(2) : $this->never())
                     ->method('innerJoin')
                     ->withConsecutive(
                         ['m.craftingCategories', 'cc'],
                         ['m.modCombinations', 'mc']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withCraftingCategories ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['cc.name IN (:craftingCategories)'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withCraftingCategories ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['craftingCategories', $craftingCategories],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withCraftingCategories ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withCraftingCategories ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        /* @var MachineRepository|MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->setMethods(['mapMachineDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withCraftingCategories ? $this->once() : $this->never())
                   ->method('mapMachineDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByCraftingCategories($craftingCategories, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Tests the mapMachineDataResult method.
     * @throws ReflectionException
     * @covers ::mapMachineDataResult
     */
    public function testMapMachineDataResult(): void
    {
        $machineData = [
            ['id' => 42],
            ['id' => 1337]
        ];
        $expectedResult = [
            (new MachineData())->setId(42),
            (new MachineData())->setId(1337),
        ];

        /* @var MachineRepository $repository */
        $repository = $this->createMock(MachineRepository::class);

        $result = $this->invokeMethod($repository, 'mapMachineDataResult', $machineData);
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
     * @covers ::findByIds
     * @dataProvider provideFindByIds
     */
    public function testFindByIds(bool $withIds): void
    {
        $ids = $withIds ? [42, 1337] : [];
        $queryResult = $withIds ? [$this->createMock(Machine::class)] : [];

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
                     ->with(['m', 'cc'])
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Machine::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('leftJoin')
                     ->with('m.craftingCategories', 'cc')
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('m.id IN (:ids)')
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

        $repository = new MachineRepository($entityManager);

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
     * @covers ::removeOrphans
     * @dataProvider provideRemoveOrphans
     */
    public function testRemoveOrphans(array $orphanedIds, bool $expectRemove): void
    {
        /* @var MachineRepository|MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
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
                     ->with('m.id AS id')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(Machine::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with('m.modCombinations', 'mc')
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

        $repository = new MachineRepository($entityManager);

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
        $machineIds = [42, 1337];

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
                     ->with(Machine::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('m.id IN (:machineIds)')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with('machineIds', $machineIds)
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

        $repository = new MachineRepository($entityManager);

        $this->invokeMethod($repository, 'removeIds', $machineIds);
    }
}
