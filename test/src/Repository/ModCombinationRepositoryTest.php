<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\ModCombination;
use FactorioItemBrowser\Api\Database\Repository\ModCombinationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ModCombinationRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\ModCombinationRepository
 */
class ModCombinationRepositoryTest extends TestCase
{
    /**
     * Provides the data for the findByModNames test.
     * @return array
     */
    public function provideFindByModNames(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findByModNames method.
     * @param bool $withModNames
     * @covers ::findByModNames
     * @dataProvider provideFindByModNames
     */
    public function testFindByModNames(bool $withModNames)
    {
        $modNames = $withModNames ? ['abc', 'def'] : [];
        $queryResult = $withModNames ? [$this->createMock(ModCombination::class)] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withModNames ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['innerJoin', 'andWhere', 'addOrderBy', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('mc.mod', 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('m.name IN (:modNames)')
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('addOrderBy')
                     ->with('mc.order', 'ASC')
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('modNames', $modNames)
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ModCombinationRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ModCombinationRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withModNames ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('mc')
                   ->willReturn($queryBuilder);

        $result = $repository->findByModNames($modNames);
        $this->assertSame($queryResult, $result);
    }

    /**
     * Provides the data for the findModNamesByIds test.
     * @return array
     */
    public function provideFindModNamesByIds(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findModNamesByIds method.
     * @param bool $withModCombinationIds
     * @covers ::findModNamesByIds
     * @dataProvider provideFindModNamesByIds
     */
    public function testFindModNamesByIds(bool $withModCombinationIds)
    {
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withModCombinationIds ? [['name' => 'abc'], ['name' => 'def']] : [];
        $expectedResult = $withModCombinationIds ? ['abc', 'def'] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withModCombinationIds ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('select')
                     ->with('m.name')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('mc.mod', 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('mc.id IN (:modCombinationIds)')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('modCombinationIds', $modCombinationIds)
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var ModCombinationRepository|MockObject $repository */
        $repository = $this->getMockBuilder(ModCombinationRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withModCombinationIds ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('mc')
                   ->willReturn($queryBuilder);

        $result = $repository->findModNamesByIds($modCombinationIds);
        $this->assertSame($expectedResult, $result);
    }
}
