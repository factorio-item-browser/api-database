<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository\Feature;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowserTestAsset\Api\Database\TestRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use ReflectionException;
use stdClass;

/**
 * The PHPUnit test of the RemoveOrphansTrait class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\Feature\RemoveOrphansTrait
 */
class RemoveOrphansTraitTest extends TestCase
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
     * @return TestRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): TestRepository
    {
        return $this->getMockBuilder(TestRepository::class)
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMock();
    }

    public function testRemoveOrphans(): void
    {
        $ids = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];

        $instance = $this->createInstance(['findOrphanedIds', 'removeIds']);
        $instance->expects($this->once())
                  ->method('findOrphanedIds')
                  ->willReturn($ids);
        $instance->expects($this->once())
                 ->method('removeIds')
                 ->with($this->identicalTo($ids));

        $instance->removeOrphans();
    }

    public function testRemoveOrphansWithoutIds(): void
    {
        $instance = $this->createInstance(['findOrphanedIds', 'removeIds']);
        $instance->expects($this->once())
                 ->method('findOrphanedIds')
                 ->willReturn([]);
        $instance->expects($this->never())
                 ->method('removeIds');

        $instance->removeOrphans();
    }

    /**
     * @throws ReflectionException
     */
    public function testFindOrphanedIds(): void
    {
        $id1 = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $id2 = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');

        $queryResult = [
            ['id' => $id1],
            ['id' => $id2],
        ];
        $expectedResult = [$id1, $id2];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('e.id AS id'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(stdClass::class), $this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance(['addRemoveOrphansConditions']);
        $instance->expects($this->once())
                 ->method('addRemoveOrphansConditions');

        $result = $this->invokeMethod($instance, 'findOrphanedIds');

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRemoveIds(): void
    {
        $id1 = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $id2 = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');


        $ids = [$id1, $id2];
        $mappedIds = [$id1->getBytes(), $id2->getBytes()];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($this->identicalTo(stdClass::class), $this->identicalTo('e'))
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

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'removeIds', $ids);
    }
}