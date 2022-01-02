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
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the MachineRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\MachineRepository
 */
class MachineRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return MachineRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): MachineRepository
    {
        return $this->getMockBuilder(MachineRepository::class)
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMock();
    }


    public function testFindByIds(): void
    {
        $ids = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $mappedIds = ['def', 'ghi'];
        $queryResult = [
            $this->createMock(Machine::class),
            $this->createMock(Machine::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

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


        $instance = $this->createInstance(['mapIdsToParameterValues']);
        $instance->expects($this->once())
                 ->method('mapIdsToParameterValues')
                 ->with($this->identicalTo($ids))
                 ->willReturn($mappedIds);

        $result = $instance->findByIds($ids);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByIdsWithoutIds(): void
    {
        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance(['mapIdsToParameterValues']);
        $instance->expects($this->never())
                 ->method('mapIdsToParameterValues');

        $result = $instance->findByIds([]);

        $this->assertSame([], $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEntityClass(): void
    {
        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'getEntityClass');

        $this->assertSame(Machine::class, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with($this->identicalTo('abc.combinations'), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id IS NULL'))
                     ->willReturnSelf();

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'addOrphanConditions', $queryBuilder, $alias);
    }

    /**
     * @throws ReflectionException
     */
    public function testRemoveIds(): void
    {
        $ids = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];

        $craftingCategories1 = $this->createMock(Collection::class);
        $craftingCategories1->expects($this->once())
                            ->method('clear');

        $craftingCategories2 = $this->createMock(Collection::class);
        $craftingCategories2->expects($this->once())
                            ->method('clear');

        $machine1 = $this->createMock(Machine::class);
        $machine1->expects($this->once())
                 ->method('getCraftingCategories')
                 ->willReturn($craftingCategories1);

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

        $instance = $this->createInstance(['findByIds']);
        $instance->expects($this->once())
                 ->method('findByIds')
                 ->with($this->identicalTo($ids))
                 ->willReturn($machines);

        $this->invokeMethod($instance, 'removeIds', $ids);
    }

    public function testFindDataByNames(): void
    {
        $names = ['abc', 'def'];
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            $this->createMock(Machine::class),
            $this->createMock(Machine::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

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

        $instance = $this->createInstance();
        $result = $instance->findByNames($combinationId, $names);

        $this->assertSame($queryResult, $result);
    }

    public function testFindDataByNamesWithoutNames(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance();
        $result = $instance->findByNames($combinationId, []);

        $this->assertSame([], $result);
    }

    public function testFindByCraftingCategoryName(): void
    {
        $craftingCategoryName = 'abc';
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            $this->createMock(Machine::class),
            $this->createMock(Machine::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

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

        $instance = $this->createInstance();
        $result = $instance->findByCraftingCategoryName($combinationId, $craftingCategoryName);

        $this->assertSame($queryResult, $result);
    }
}
