<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Repository\ModRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ModRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\ModRepository
 */
class ModRepositoryTest extends TestCase
{
    /**
     * Provides the data for the findByNamesWithDependencies test.
     * @return array
     */
    public function provideFindByNamesWithDependencies(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findByNamesWithDependencies method.
     * @param bool $withModNames
     * @covers ::findByNamesWithDependencies
     * @dataProvider provideFindByNamesWithDependencies
     */
    public function testFindByNamesWithDependencies(bool $withModNames): void
    {
        $modNames = $withModNames ? ['abc', 'def'] : [];
        $queryResult = $withModNames ? [$this->createMock(Mod::class)] : [];

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
                             ->setMethods(['select', 'from', 'leftJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('select')
                     ->with(['m', 'd', 'dm'])
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Mod::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->exactly(2) : $this->never())
                     ->method('leftJoin')
                     ->withConsecutive(
                         ['m.dependencies', 'd'],
                         ['d.requiredMod', 'dm']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('m.name IN (:modNames)')
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('modNames', $modNames)
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withModNames ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new ModRepository($entityManager);

        $result = $repository->findByNamesWithDependencies($modNames);
        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findAll method.
     * @covers ::findAll
     */
    public function testFindAll(): void
    {
        $queryResult = [
            new Mod('abc'),
            new Mod('def'),
        ];

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
                             ->setMethods(['select', 'from', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('m')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(Mod::class, 'm')
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

        $repository = new ModRepository($entityManager);

        $result = $repository->findAll();
        $this->assertSame($queryResult, $result);
    }

    /**
     * Provides the data for the count test.
     * @return array
     */
    public function provideCount(): array
    {
        return [
            [true, false],
            [false, false],
            [true, true],
            [false, true],
        ];
    }

    /**
     * Tests the count method.
     * @param bool $withModCombinationIds
     * @param bool $withException
     * @covers ::count
     * @dataProvider provideCount
     */
    public function testCount(bool $withModCombinationIds, bool $withException): void
    {
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = 21;
        $expectedResult = $withException ? 0 : $queryResult;

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getSingleScalarResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();

        if ($withException) {
            $query->expects($this->once())
                  ->method('getSingleScalarResult')
                  ->willThrowException(new NonUniqueResultException());
        } else {
            $query->expects($this->once())
                  ->method('getSingleScalarResult')
                  ->willReturn($queryResult);
        }

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('COUNT(DISTINCT m.id) AS numberOfMods')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(Mod::class, 'm')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('m.combinations', 'mc')
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

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new ModRepository($entityManager);

        $result = $repository->count($modCombinationIds);
        $this->assertSame($expectedResult, $result);
    }
}
