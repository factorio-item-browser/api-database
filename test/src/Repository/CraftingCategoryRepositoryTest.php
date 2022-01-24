<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Repository\CategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the CraftingCategoryRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\CategoryRepository
 */
class CraftingCategoryRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    private function createInstance(): CategoryRepository
    {
        return new CategoryRepository(
            $this->entityManager,
        );
    }

    public function testFindByNames(): void
    {
        $names = ['abc', 'def'];
        $queryResult = [
            $this->createMock(CraftingCategory::class),
            $this->createMock(CraftingCategory::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('cc'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(CraftingCategory::class), $this->identicalTo('cc'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('where')
                     ->with($this->identicalTo('cc.name IN (:names)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with($this->identicalTo('names'), $this->identicalTo($names))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance();
        $result = $instance->findByNames($names);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByNamesWithoutNames(): void
    {
        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance();
        $result = $instance->findByNames([]);

        $this->assertSame([], $result);
    }


    /**
     * @throws ReflectionException
     */
    public function testGetEntityClass(): void
    {
        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'getEntityClass');

        $this->assertSame(CraftingCategory::class, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->exactly(2))
                     ->method('leftJoin')
                     ->withConsecutive(
                         [
                             $this->identicalTo('abc.machines'),
                             $this->identicalTo('m'),
                         ],
                         [
                             $this->identicalTo('abc.recipes'),
                             $this->identicalTo('r'),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('m.id IS NULL')],
                         [$this->identicalTo('r.id IS NULL')]
                     )
                     ->willReturnSelf();

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'addOrphanConditions', $queryBuilder, $alias);
    }
}
