<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Repository\CraftingCategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the CraftingCategoryRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\CraftingCategoryRepository
 */
class CraftingCategoryRepositoryTest extends TestCase
{
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
    public function testFindByNames(bool $withNames)
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
                             ->setMethods(['andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
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

        /* @var CraftingCategoryRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CraftingCategoryRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withNames ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('cc')
                   ->willReturn($queryBuilder);

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
            [
                [['id' => 42], ['id' => 1337]],
                [42, 1337]
            ],
            [
                [],
                []
            ]
        ];
    }

    /**
     * Tests the removeOrphans method.
     * @param array $firstResult
     * @param array $expectedCraftingCategoryIds
     * @covers ::removeOrphans
     * @dataProvider provideRemoveOrphans
     */
    public function testRemoveOrphans(array $firstResult, array $expectedCraftingCategoryIds)
    {
        $entityName = 'abc';
        $expectSecondQuery = count($expectedCraftingCategoryIds) > 0;

        /* @var AbstractQuery|MockObject $query1 */
        $query1 = $this->getMockBuilder(AbstractQuery::class)
                       ->setMethods(['getResult'])
                       ->disableOriginalConstructor()
                       ->getMockForAbstractClass();
        $query1->expects($this->once())
               ->method('getResult')
               ->willReturn($firstResult);

        /* @var QueryBuilder|MockObject $queryBuilder1 */
        $queryBuilder1 = $this->getMockBuilder(QueryBuilder::class)
                              ->setMethods(['select', 'leftJoin', 'andWhere', 'getQuery'])
                              ->disableOriginalConstructor()
                              ->getMock();
        $queryBuilder1->expects($this->once())
                      ->method('select')
                      ->with('cc.id AS id')
                      ->willReturnSelf();
        $queryBuilder1->expects($this->exactly(2))
                      ->method('leftJoin')
                      ->withConsecutive(
                          ['cc.machines', 'm'],
                          ['cc.recipes', 'r']
                      )
                      ->willReturnSelf();
        $queryBuilder1->expects($this->exactly(2))
                      ->method('andWhere')
                      ->withConsecutive(
                          ['m.id IS NULL'],
                          ['r.id IS NULL']
                      )
                      ->willReturnSelf();
        $queryBuilder1->expects($this->once())
                      ->method('getQuery')
                      ->willReturn($query1);

        $queryBuilder2 = null;
        if ($expectSecondQuery) {
            /* @var AbstractQuery|MockObject $query2 */
            $query2 = $this->getMockBuilder(AbstractQuery::class)
                           ->setMethods(['execute'])
                           ->disableOriginalConstructor()
                           ->getMockForAbstractClass();
            $query2->expects($this->once())
                   ->method('execute');

            /* @var QueryBuilder|MockObject $queryBuilder2 */
            $queryBuilder2 = $this->getMockBuilder(QueryBuilder::class)
                                  ->setMethods(['delete', 'andWhere', 'setParameter', 'getQuery'])
                                  ->disableOriginalConstructor()
                                  ->getMock();
            $queryBuilder2->expects($this->once())
                          ->method('delete')
                          ->with($entityName, 'cc')
                          ->willReturnSelf();
            $queryBuilder2->expects($this->once())
                          ->method('andWhere')
                          ->with('cc.id IN (:craftingCategoryIds)')
                          ->willReturnSelf();
            $queryBuilder2->expects($this->once())
                          ->method('setParameter')
                          ->with('craftingCategoryIds', $expectedCraftingCategoryIds)
                          ->willReturnSelf();
            $queryBuilder2->expects($this->once())
                          ->method('getQuery')
                          ->willReturn($query2);
        }

        /* @var CraftingCategoryRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CraftingCategoryRepository::class)
                           ->setMethods(['createQueryBuilder', 'getEntityName'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->exactly($expectSecondQuery ? 2 : 1))
                   ->method('createQueryBuilder')
                   ->with('cc')
                   ->willReturnOnConsecutiveCalls($queryBuilder1, $queryBuilder2);
        $repository->expects($expectSecondQuery ? $this->once() : $this->never())
                   ->method('getEntityName')
                   ->willReturn($entityName);

        $this->assertSame($repository, $repository->removeOrphans());
    }
}
