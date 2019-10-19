<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Repository\AbstractIdRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractIdRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\AbstractIdRepository
 */
class AbstractIdRepositoryTest extends TestCase
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
     * Tests the findByIds method.
     * @covers ::findByIds
     */
    public function testFindByIds(): void
    {
        $entityClass = 'abc';
        $ids = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $mappedIds = ['def', 'ghi'];
        $queryResult = [
            $this->createMock(Combination::class),
            $this->createMock(Combination::class),
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
                     ->with($this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo($entityClass), $this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('e.id IN (:ids)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('ids'),
                         $this->identicalTo($mappedIds)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        /* @var AbstractIdRepository&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepository::class)
                           ->onlyMethods(['getEntityClass', 'mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();
        $repository->expects($this->once())
                   ->method('getEntityClass')
                   ->willReturn($entityClass);
        $repository->expects($this->once())
                   ->method('mapIdsToParameterValues')
                   ->with($this->identicalTo($ids))
                   ->willReturn($mappedIds);

        $result = $repository->findByIds($ids);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByIds method.
     * @covers ::findByIds
     */
    public function testFindByIdsWithoutIds(): void
    {
        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var AbstractIdRepository&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepository::class)
                           ->onlyMethods(['getEntityClass', 'mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();
        $repository->expects($this->never())
                   ->method('getEntityClass');
        $repository->expects($this->never())
                   ->method('mapIdsToParameterValues');

        $result = $repository->findByIds([]);

        $this->assertSame([], $result);
    }

    /**
     * Tests the mapIdsToParameterValues method.
     * @throws ReflectionException
     * @covers ::mapIdsToParameterValues
     */
    public function testMapIdsToParameterValues(): void
    {
        $expectedResult = ['abc', 'def'];

        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        $id1->expects($this->once())
            ->method('getBytes')
            ->willReturn('abc');

        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);
        $id2->expects($this->once())
            ->method('getBytes')
            ->willReturn('def');

        /* @var AbstractIdRepository&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepository::class)
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();

        $result = $this->invokeMethod($repository, 'mapIdsToParameterValues', [$id1, $id2]);

        $this->assertSame($expectedResult, $result);
    }
}
