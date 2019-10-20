<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Data\MachineData;
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

    /**
     * Tests the findDataByNames method.
     * @covers ::findDataByNames
     */
    public function testFindDataByNames(): void
    {
        $names = ['abc', 'def'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            ['id' => $this->createMock(UuidInterface::class)],
            ['id' => $this->createMock(UuidInterface::class)],
        ];
        $mappedResult = [
            $this->createMock(MachineData::class),
            $this->createMock(MachineData::class),
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
                     ->with($this->identicalTo([
                         'm.id AS id',
                         'm.name AS name',
                     ]))
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

        /* @var MachineRepository&MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->onlyMethods(['mapMachineDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('mapMachineDataResult')
                   ->with($this->identicalTo($queryResult))
                   ->willReturn($mappedResult);

        $result = $repository->findDataByNames($combinationId, $names);

        $this->assertSame($mappedResult, $result);
    }

    /**
     * Tests the findDataByNames method.
     * @covers ::findDataByNames
     */
    public function testFindDataByNamesWithoutNames(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var MachineRepository&MockObject $repository */
        $repository = $this->getMockBuilder(MachineRepository::class)
                           ->onlyMethods(['mapMachineDataResult'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapMachineDataResult');

        $result = $repository->findDataByNames($combinationId, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the mapMachineDataResult method.
     * @throws ReflectionException
     * @covers ::mapMachineDataResult
     */
    public function testMapMachineDataResult(): void
    {
        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);

        $machineData = [
            [
                'id' => $id1,
                'name' => 'abc',
            ],
            [
                'id' => $id2,
                'name' => 'def',
            ],
        ];

        $data1 = new MachineData();
        $data1->setId($id1)
              ->setName('abc');
        $data2 = new MachineData();
        $data2->setId($id2)
              ->setName('def');
        $expectedResult = [$data1, $data2];

        $repository = new MachineRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'mapMachineDataResult', $machineData);

        $this->assertEquals($expectedResult, $result);
    }
}
