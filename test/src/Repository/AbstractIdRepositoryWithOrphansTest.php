<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Repository\AbstractIdRepositoryWithOrphans;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractIdRepositoryWithOrphans class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\AbstractIdRepositoryWithOrphans
 */
class AbstractIdRepositoryWithOrphansTest extends TestCase
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
     * Tests the removeOrphans method.
     * @covers ::removeOrphans
     */
    public function testRemoveOrphans(): void
    {
        $ids = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];

        /* @var AbstractIdRepositoryWithOrphans&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepositoryWithOrphans::class)
                           ->onlyMethods(['findOrphanedIds', 'removeIds'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();
        $repository->expects($this->once())
                   ->method('findOrphanedIds')
                   ->willReturn($ids);
        $repository->expects($this->once())
                   ->method('removeIds')
                   ->with($this->identicalTo($ids));

        $repository->removeOrphans();
    }

    /**
     * Tests the removeOrphans method.
     * @covers ::removeOrphans
     */
    public function testRemoveOrphansWithoutIds(): void
    {
        /* @var AbstractIdRepositoryWithOrphans&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepositoryWithOrphans::class)
                           ->onlyMethods(['findOrphanedIds', 'removeIds'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();
        $repository->expects($this->once())
                   ->method('findOrphanedIds')
                   ->willReturn([]);
        $repository->expects($this->never())
                   ->method('removeIds');

        $repository->removeOrphans();
    }

    /**
     * Tests the findOrphanedIds method.
     * @throws ReflectionException
     * @covers ::findOrphanedIds
     */
    public function testFindOrphanedIds(): void
    {
        $entityClass = 'abc';

        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $id1],
            ['id' => $id2],
        ];
        $expectedResult = [$id1, $id2];

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);


        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('e.id AS id'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo($entityClass), $this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        /* @var AbstractIdRepositoryWithOrphans&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepositoryWithOrphans::class)
                           ->onlyMethods(['getEntityClass', 'addOrphanConditions'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();
        $repository->expects($this->once())
                   ->method('getEntityClass')
                   ->willReturn($entityClass);
        $repository->expects($this->once())
                   ->method('addOrphanConditions');

        $result = $this->invokeMethod($repository, 'findOrphanedIds');

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the removeIds method.
     * @throws ReflectionException
     * @covers ::removeIds
     */
    public function testRemoveIds(): void
    {
        $entityClass = 'abc';
        $ids = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $mappedIds = ['def', 'ghi'];

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($this->identicalTo($entityClass), $this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('e.id IN (:ids)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with($this->identicalTo('ids'), $this->identicalTo($mappedIds))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        /* @var AbstractIdRepositoryWithOrphans&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractIdRepositoryWithOrphans::class)
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

        $this->invokeMethod($repository, 'removeIds', $ids);
    }
}
