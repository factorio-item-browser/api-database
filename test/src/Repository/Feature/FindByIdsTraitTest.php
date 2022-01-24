<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository\Feature;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsInterface;
use FactorioItemBrowserTestAsset\Api\Database\TestRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * The PHPUnit test of the FindByIdsTrait class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\Feature\FindByIdsTrait
 */
class FindByIdsTraitTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @return FindByIdsInterface<stdClass>
     */
    private function createInstance(): FindByIdsInterface
    {
        return new TestRepository(
            $this->entityManager,
        );
    }

    public function testFindByIds(): void
    {
        $id1 = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $id2 = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');

        $ids = [$id1, $id2];
        $mappedIds = [$id1->getBytes(), $id2->getBytes()];
        $queryResult = [
            $this->createMock(stdClass::class),
            $this->createMock(stdClass::class),
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
                     ->with($this->identicalTo(stdClass::class), $this->identicalTo('e'))
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

        $instance = $this->createInstance();
        $result = $instance->findByIds($ids);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByIdsWithoutIds(): void
    {
        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance();
        $result = $instance->findByIds([]);

        $this->assertSame([], $result);
    }
}
