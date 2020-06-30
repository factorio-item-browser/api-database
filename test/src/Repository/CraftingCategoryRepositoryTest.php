<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Repository\CraftingCategoryRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the CraftingCategoryRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\CraftingCategoryRepository
 */
class CraftingCategoryRepositoryTest extends TestCase
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
     * Tests the findByNames method.
     * @covers ::findByNames
     */
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

        $repository = new CraftingCategoryRepository($this->entityManager);
        $result = $repository->findByNames($names);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByNames method.
     * @covers ::findByNames
     */
    public function testFindByNamesWithoutNames(): void
    {
        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $repository = new CraftingCategoryRepository($this->entityManager);
        $result = $repository->findByNames([]);

        $this->assertSame([], $result);
    }


    /**
     * Tests the getEntityClass method.
     * @throws ReflectionException
     * @covers ::getEntityClass
     */
    public function testGetEntityClass(): void
    {
        $repository = new CraftingCategoryRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(CraftingCategory::class, $result);
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

        $repository = new CraftingCategoryRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }
}
