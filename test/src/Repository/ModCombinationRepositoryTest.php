<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Repository\ModCombinationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

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
     * @throws ReflectionException
     * @covers ::findByNames
     * @dataProvider provideFindByNames
     */
    public function testFindByNames(bool $withNames): void
    {
        $names = $withNames ? ['abc', 'def'] : [];
        $queryResult = $withNames ? [$this->createMock(Combination::class)] : [];

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
                             ->setMethods(['select', 'from', 'andWhere', 'addOrderBy', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('select')
                     ->with('mc')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Combination::class, 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('mc.name IN (:names)')
                     ->willReturnSelf();
        $queryBuilder->expects($withNames ? $this->once() : $this->never())
                     ->method('addOrderBy')
                     ->with('mc.order', 'ASC')
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

        $repository = new ModCombinationRepository($entityManager);

        $result = $repository->findByNames($names);
        $this->assertSame($queryResult, $result);
    }
    
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
     * @throws ReflectionException
     * @covers ::findByModNames
     * @dataProvider provideFindByModNames
     */
    public function testFindByModNames(bool $withModNames): void
    {
        $modNames = $withModNames ? ['abc', 'def'] : [];
        $queryResult = $withModNames ? [$this->createMock(Combination::class)] : [];

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
                             ->setMethods([
                                 'select',
                                 'from',
                                 'innerJoin',
                                 'andWhere',
                                 'addOrderBy',
                                 'setParameter',
                                 'getQuery'
                             ])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('select')
                     ->with(['mc', 'm'])
                     ->willReturnSelf();
        $queryBuilder->expects($withModNames ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Combination::class, 'mc')
                     ->willReturnSelf();
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

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withModNames ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new ModCombinationRepository($entityManager);

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
     * @throws ReflectionException
     * @covers ::findModNamesByIds
     * @dataProvider provideFindModNamesByIds
     */
    public function testFindModNamesByIds(bool $withModCombinationIds): void
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
                             ->setMethods(['select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('select')
                     ->with('m.name')
                     ->willReturnSelf();
        $queryBuilder->expects($withModCombinationIds ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Combination::class, 'mc')
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

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withModCombinationIds ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new ModCombinationRepository($entityManager);

        $result = $repository->findModNamesByIds($modCombinationIds);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the findAll method.
     * @throws ReflectionException
     * @covers ::findAll
     */
    public function testFindAll(): void
    {
        $queryResult = [
            new Combination(new Mod('abc'), 'def'),
            new Combination(new Mod('ghi'), 'jkl'),
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
                     ->with('mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(Combination::class, 'mc')
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

        $repository = new ModCombinationRepository($entityManager);

        $result = $repository->findAll();
        $this->assertSame($queryResult, $result);
    }
}
