<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\Common\Test\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Repository\CraftingCategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the CraftingCategoryRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\CraftingCategoryRepository
 */
class CraftingCategoryRepositoryTest extends TestCase
{
    use ReflectionTrait;
    
    /**
     * Provides the data for the findByNames test.
     * @return array
     */
    public function provideFindByNames(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findByNames method.
     * @param bool $withNames
     * @covers ::findByNames
     * @dataProvider provideFindByNames
     */
    public function testFindByNames(bool $withNames): void
    {
        $names = $withNames ? ['abc', 'def'] : [];
        $queryResult = $withNames ? [$this->createMock(CraftingCategory::class)] : [];

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
                             ->setMethods(['select', 'from', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('select')
                     ->with('cc')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('from')
                     ->with(CraftingCategory::class, 'cc')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('cc.name IN (:names)')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('names', $names)
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

        $repository = new CraftingCategoryRepository($entityManager);

        $result = $repository->findByNames($names);
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
        /* @var CraftingCategoryRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CraftingCategoryRepository::class)
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
                     ->with('cc.id AS id')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(CraftingCategory::class, 'cc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('leftJoin')
                     ->withConsecutive(
                         ['cc.machines', 'm'],
                         ['cc.recipes', 'r']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['m.id IS NULL'],
                         ['r.id IS NULL']
                     )
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

        $repository = new CraftingCategoryRepository($entityManager);

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
        $craftingCategoryIds = [42, 1337];

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
                     ->with(CraftingCategory::class, 'cc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('cc.id IN (:craftingCategoryIds)')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with('craftingCategoryIds', $craftingCategoryIds)
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

        $repository = new CraftingCategoryRepository($entityManager);

        $this->invokeMethod($repository, 'removeIds', $craftingCategoryIds);
    }
}
