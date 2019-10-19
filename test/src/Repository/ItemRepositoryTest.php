<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Repository\ItemRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the ItemRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\ItemRepository
 */
class ItemRepositoryTest extends TestCase
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
        $repository = new ItemRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(Item::class, $result);
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
        $queryBuilder->expects($this->exactly(3))
                     ->method('leftJoin')
                     ->withConsecutive(
                         [
                             $this->identicalTo('abc.combinations'),
                             $this->identicalTo('c'),
                         ],
                         [
                             $this->identicalTo(RecipeIngredient::class),
                             $this->identicalTo('ri'),
                             $this->identicalTo('WITH'),
                             $this->identicalTo('ri.item = abc.id'),
                         ],
                         [
                             $this->identicalTo(RecipeProduct::class),
                             $this->identicalTo('rp'),
                             $this->identicalTo('WITH'),
                             $this->identicalTo('rp.item = abc.id'),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('c.id IS NULL')],
                         [$this->identicalTo('ri.item IS NULL')],
                         [$this->identicalTo('rp.item IS NULL')]
                     )
                     ->willReturnSelf();

        $repository = new ItemRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }

    /**
     * Tests the findByTypeAndNames method.
     * @covers ::findByTypeAndNames
     */
    public function testFindByTypeAndNames(): void
    {
        $type = 'abc';
        $names = ['def', 'ghi'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Item::class),
            $this->createMock(Item::class),
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
                     ->with($this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Item::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('i.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('i.type = :type')],
                         [$this->identicalTo('i.name IN (:names)')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('type'),
                             $this->identicalTo($type)
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

        $repository = new ItemRepository($this->entityManager);
        $result = $repository->findByTypeAndNames($combinationId, $type, $names);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByTypeAndNames method.
     * @covers ::findByTypeAndNames
     */
    public function testFindByTypeAndNamesWithoutNames(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $repository = new ItemRepository($this->entityManager);
        $result = $repository->findByTypeAndNames($combinationId, 'abc', []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findByKeywords method.
     * @covers ::findByKeywords
     */
    public function testFindByKeywords(): void
    {
        $keywords = ['foo', 'b_a\\r%'];

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Item::class),
            $this->createMock(Item::class),
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
                     ->with($this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Item::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('i.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('i.name LIKE :keyword0')],
                         [$this->identicalTo('i.name LIKE :keyword1')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME)
                         ],
                         [
                             $this->identicalTo('keyword0'),
                             $this->identicalTo('%foo%')
                         ],
                         [
                             $this->identicalTo('keyword1'),
                             $this->identicalTo('%b\\_a\\\\r\\%%')
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new ItemRepository($this->entityManager);
        $result = $repository->findByKeywords($combinationId, $keywords);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByKeywords method.
     * @covers ::findByKeywords
     */
    public function testFindByKeywordsWithoutKeywords(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $repository = new ItemRepository($this->entityManager);
        $result = $repository->findByKeywords($combinationId, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findRandom method.
     * @covers ::findRandom
     */
    public function testFindRandom(): void
    {
        $numberOfItems = 42;

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Item::class),
            $this->createMock(Item::class),
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
                     ->with($this->identicalTo('i'), $this->identicalTo('RAND() AS HIDDEN rand'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Item::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('i.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('combinationId'),
                         $this->identicalTo($combinationId),
                         $this->identicalTo(UuidBinaryType::NAME)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('addOrderBy')
                     ->with($this->identicalTo('rand'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with($this->identicalTo($numberOfItems))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new ItemRepository($this->entityManager);
        $result = $repository->findRandom($combinationId, $numberOfItems);

        $this->assertSame($queryResult, $result);
    }
}
