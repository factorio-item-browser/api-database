<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\Common\Test\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\IconData;
use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Repository\IconRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the IconRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\IconRepository
 */
class IconRepositoryTest extends TestCase
{
    use ReflectionTrait;
    
    /**
     * Provides the data for the findDataByTypesAndNames test.
     * @return array
     */
    public function provideFindDataByTypesAndNames(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByTypesAndNames method.
     * @param bool $withNamesByTypes
     * @param bool $withModCombinationIds
     * @throws ReflectionException
     * @covers ::findDataByTypesAndNames
     * @dataProvider provideFindDataByTypesAndNames
     */
    public function testFindDataByTypesAndNames(bool $withNamesByTypes, bool $withModCombinationIds): void
    {
        $namesByTypes = $withNamesByTypes ? ['foo' => ['abc', 'def'], 'bar' => ['ghi']] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withNamesByTypes ? [['id' => 42]] : [];
        $dataResult = $withNamesByTypes ? [$this->createMock(IconData::class)] : [];

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
                             ->setMethods(['select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with([
                         'i.id AS id',
                         'IDENTITY(i.file) AS hash',
                         'i.type AS type',
                         'i.name AS name',
                         'mc.order AS order',
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(Icon::class, 'i')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with('i.modCombination', 'mc')
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

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        /* @var IconRepository|MockObject $repository */
        $repository = $this->getMockBuilder(IconRepository::class)
                           ->setMethods(['mapIconDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withNamesByTypes ? $this->once() : $this->never())
                   ->method('mapIconDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByTypesAndNames($namesByTypes, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }

    /**
     * Provides the data for the findDataByHashes test.
     * @return array
     */
    public function provideFindDataByHashes(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }

    /**
     * Tests the findDataByHashes method.
     * @param bool $withHashes
     * @param bool $withModCombinationIds
     * @throws ReflectionException
     * @covers ::findDataByHashes
     * @dataProvider provideFindDataByHashes
     */
    public function testFindDataByHashes(bool $withHashes, bool $withModCombinationIds): void
    {
        $hashes = $withHashes ? ['ab12cd34', '12ab34cd'] : [];
        $expectedHashes = $withHashes ? [hex2bin('ab12cd34'), hex2bin('12ab34cd')] : [];
        $modCombinationIds = $withModCombinationIds ? [42, 1337] : [];
        $queryResult = $withHashes ? [['id' => 42]] : [];
        $dataResult = $withHashes ? [$this->createMock(IconData::class)] : [];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withHashes ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'innerJoin', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('select')
                     ->with([
                         'i.id AS id',
                         'IDENTITY(i.file) AS hash',
                         'i.type AS type',
                         'i.name AS name',
                         'mc.order AS order',
                     ])
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Icon::class, 'i')
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('innerJoin')
                     ->with('i.modCombination', 'mc')
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withHashes ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['i.file IN (:hashes)'],
                         ['mc.id IN (:modCombinationIds)']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly($withHashes ? $withModCombinationIds ? 2 : 1 : 0))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['hashes', $expectedHashes],
                         ['modCombinationIds', $modCombinationIds]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withHashes ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        /* @var IconRepository|MockObject $repository */
        $repository = $this->getMockBuilder(IconRepository::class)
                           ->setMethods(['mapIconDataResult'])
                           ->setConstructorArgs([$entityManager])
                           ->getMock();
        $repository->expects($withHashes ? $this->once() : $this->never())
                   ->method('mapIconDataResult')
                   ->with($queryResult)
                   ->willReturn($dataResult);

        $result = $repository->findDataByHashes($hashes, $modCombinationIds);
        $this->assertSame($dataResult, $result);
    }
    
    /**
     * Tests the mapIconDataResult method.
     * @throws ReflectionException
     * @covers ::mapIconDataResult
     */
    public function testMapIconDataResult(): void
    {
        $iconData = [
            ['id' => 42],
            ['id' => 1337]
        ];
        $expectedResult = [
            (new IconData())->setId(42),
            (new IconData())->setId(1337),
        ];

        /* @var EntityManagerInterface $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $repository = new IconRepository($entityManager);

        $result = $this->invokeMethod($repository, 'mapIconDataResult', $iconData);
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
        $queryResult = $withIds ? [$this->createMock(Icon::class)] : [];

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
                             ->setMethods(['select', 'from', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('select')
                     ->with('i')
                     ->willReturnSelf();
        $queryBuilder->expects($withIds ? $this->once() : $this->never())
                     ->method('from')
                     ->with(Icon::class, 'i')
                     ->willReturnSelf();
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

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withIds ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new IconRepository($entityManager);

        $result = $repository->findByIds($ids);
        $this->assertSame($queryResult, $result);
    }
}
