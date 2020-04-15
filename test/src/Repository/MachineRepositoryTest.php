<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use FactorioItemBrowser\Api\Database\Repository\MachineRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;
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
        $ids = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $mappedIds = ['def', 'ghi'];
        $queryResult = [
            $this->createMock(Machine::class),
            $this->createMock(Machine::class),
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
                     ->with($this->identicalTo('m'), $this->identicalTo('cc'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Machine::class), $this->identicalTo('m'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with($this->identicalTo('m.craftingCategories'), $this->identicalTo('cc'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('m.id IN (:ids)'))
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

        /* @var MachineRepository&MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->onlyMethods(['mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
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

        /* @var MachineRepository&MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->onlyMethods(['getEntityClass', 'mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapIdsToParameterValues');

        $result = $repository->findByIds([]);

        $this->assertSame([], $result);
    }

    /**
     * Tests the getEntityClass method.
     * @throws ReflectionException
     * @covers ::getEntityClass
     */
    public function testGetEntityClass(): void
    {
        $repository = new MachineRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(Machine::class, $result);
    }

    /**
     * Tests the addOrphanConditions method.
     * @throws ReflectionException
     * @covers ::addOrphanConditions
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with($this->identicalTo('abc.combinations'), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id IS NULL'))
                     ->willReturnSelf();

        $repository = new MachineRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }

    /**
     * Tests the removeIds method.
     * @throws ReflectionException
     * @covers ::removeIds
     */
    public function testRemoveIds(): void
    {
        $ids = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];

        /* @var Collection&MockObject $craftingCategories1 */
        $craftingCategories1 = $this->createMock(Collection::class);
        $craftingCategories1->expects($this->once())
                            ->method('clear');

        /* @var Collection&MockObject $craftingCategories2 */
        $craftingCategories2 = $this->createMock(Collection::class);
        $craftingCategories2->expects($this->once())
                            ->method('clear');

        /* @var Machine&MockObject $machine1 */
        $machine1 = $this->createMock(Machine::class);
        $machine1->expects($this->once())
                 ->method('getCraftingCategories')
                 ->willReturn($craftingCategories1);

        /* @var Machine&MockObject $machine2 */
        $machine2 = $this->createMock(Machine::class);
        $machine2->expects($this->once())
                 ->method('getCraftingCategories')
                 ->willReturn($craftingCategories2);

        $machines = [$machine1, $machine2];

        $this->entityManager->expects($this->exactly(2))
                            ->method('remove')
                            ->withConsecutive(
                                [$this->identicalTo($machine1)],
                                [$this->identicalTo($machine2)]
                            );
        $this->entityManager->expects($this->once())
                            ->method('flush');

        /* @var MachineRepository&MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->onlyMethods(['findByIds'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('findByIds')
                   ->with($this->identicalTo($ids))
                   ->willReturn($machines);

        $this->invokeMethod($repository, 'removeIds', $ids);
    }

    /**
     * Tests the findByNames method.
     * @covers ::findByNames
     */
    public function testFindDataByNames(): void
    {
        $names = ['abc', 'def'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Machine::class),
            $this->createMock(Machine::class),
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
                     ->with($this->identicalTo('m'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Machine::class), $this->identicalTo('m'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('m.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('m.name IN (:names)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('names'),
                             $this->identicalTo($names)
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new MachineRepository($this->entityManager);
        $result = $repository->findByNames($combinationId, $names);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByNames method.
     * @covers ::findByNames
     */
    public function testFindDataByNamesWithoutNames(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $repository = new MachineRepository($this->entityManager);
        $result = $repository->findByNames($combinationId, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findByCraftingCategoryName method.
     * @covers ::findByCraftingCategoryName
     */
    public function testFindByCraftingCategoryName(): void
    {
        $craftingCategoryName = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Machine::class),
            $this->createMock(Machine::class),
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
                     ->with($this->identicalTo('m'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Machine::class), $this->identicalTo('m'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('innerJoin')
                     ->withConsecutive(
                         [
                             $this->identicalTo('m.combinations'),
                             $this->identicalTo('c'),
                             $this->identicalTo('WITH'),
                             $this->identicalTo('c.id = :combinationId'),
                         ],
                         [
                             $this->identicalTo('m.craftingCategories'),
                             $this->identicalTo('cc'),
                             $this->identicalTo('WITH'),
                             $this->identicalTo('cc.name = :craftingCategoryName'),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('craftingCategoryName'),
                             $this->identicalTo($craftingCategoryName)
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new MachineRepository($this->entityManager);
        $result = $repository->findByCraftingCategoryName($combinationId, $craftingCategoryName);

        $this->assertSame($queryResult, $result);
    }
}
