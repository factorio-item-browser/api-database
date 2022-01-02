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
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the AbstractIdRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\AbstractIdRepository
 */
class AbstractIdRepositoryTest extends TestCase
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
     * @return AbstractIdRepository<object>&MockObject
     */
    private function createInstance(array $mockedMethods = []): AbstractIdRepository
    {
        return $this->getMockBuilder(AbstractIdRepository::class)
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMockForAbstractClass();
    }


    public function testFindByIds(): void
    {
        $entityClass = 'abc';
        $ids = [
            Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef'),
            Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210'),
        ];
        $mappedIds = ['def', 'ghi'];
        $queryResult = [
            $this->createMock(Combination::class),
            $this->createMock(Combination::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

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

        $instance = $this->createInstance(['getEntityClass', 'mapIdsToParameterValues']);
        $instance->expects($this->once())
                 ->method('getEntityClass')
                 ->willReturn($entityClass);
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

        $instance = $this->createInstance(['getEntityClass', 'mapIdsToParameterValues']);
        $instance->expects($this->never())
                 ->method('getEntityClass');
        $instance->expects($this->never())
                 ->method('mapIdsToParameterValues');

        $result = $instance->findByIds([]);

        $this->assertSame([], $result);
    }
}
