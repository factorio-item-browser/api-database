<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Repository\IconRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the IconRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\IconRepository
 */
class IconRepositoryTest extends TestCase
{
    /**
     * The entity manager.
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
     * Tests the findByTypesAndNames method.
     * @covers ::findByTypesAndNames
     */
    public function testFindByTypesAndNames(): void
    {
        /* @var NamesByTypes&MockObject $namesByTypes */
        $namesByTypes = $this->createMock(NamesByTypes::class);
        $namesByTypes->expects($this->once())
                     ->method('isEmpty')
                     ->willReturn(false);
        $namesByTypes->expects($this->once())
                     ->method('toArray')
                     ->willReturn([
                         'abc' => ['def', 'ghi'],
                         'jkl' => ['mno'],
                     ]);

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $queryResult = [
            $this->createMock(Icon::class),
            $this->createMock(Icon::class),
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
                     ->with($this->identicalTo(Icon::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('i.combination'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('orWhere')
                     ->withConsecutive(
                         [$this->identicalTo('i.type = :type0 AND i.name IN (:names0)')],
                         [$this->identicalTo('i.type = :type1 AND i.name IN (:names1)')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(5))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME),
                         ],
                         [
                             $this->identicalTo('type0'),
                             $this->identicalTo('abc'),
                         ],
                         [
                             $this->identicalTo('names0'),
                             $this->identicalTo(['def', 'ghi']),
                         ],
                         [
                             $this->identicalTo('type1'),
                             $this->identicalTo('jkl'),
                         ],
                         [
                             $this->identicalTo('names1'),
                             $this->identicalTo(['mno']),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new IconRepository($this->entityManager);
        $result = $repository->findByTypesAndNames($combinationId, $namesByTypes);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByTypesAndNames method.
     * @covers ::findByTypesAndNames
     */
    public function testFindByTypesAndNamesWithoutConditions(): void
    {
        /* @var NamesByTypes&MockObject $namesByTypes */
        $namesByTypes = $this->createMock(NamesByTypes::class);
        $namesByTypes->expects($this->once())
                     ->method('isEmpty')
                     ->willReturn(true);

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $repository = new IconRepository($this->entityManager);
        $result = $repository->findByTypesAndNames($combinationId, $namesByTypes);

        $this->assertSame([], $result);
    }

    /**
     * Tests the findByImageIds method.
     * @covers ::findByImageIds
     */
    public function testFindByImageIds(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $imageIds = [
            $this->createMock(UuidInterface::class),
            $this->createMock(UuidInterface::class),
        ];
        $mappedImageIds = ['abc', 'def'];
        $queryResult = [
            $this->createMock(Icon::class),
            $this->createMock(Icon::class),
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
                     ->with($this->identicalTo(Icon::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('i.combination'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('i.image IN (:imageIds)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME),
                         ],
                         [
                             $this->identicalTo('imageIds'),
                             $this->identicalTo($mappedImageIds),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        /* @var IconRepository&MockObject $repository */
        $repository = $this->getMockBuilder(IconRepository::class)
                           ->onlyMethods(['mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('mapIdsToParameterValues')
                   ->with($this->identicalTo($imageIds))
                   ->willReturn($mappedImageIds);

        $result = $repository->findByImageIds($combinationId, $imageIds);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the findByImageIds method.
     * @covers ::findByImageIds
     */
    public function testFindByImageIdsWithoutImageIds(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        /* @var IconRepository&MockObject $repository */
        $repository = $this->getMockBuilder(IconRepository::class)
                           ->onlyMethods(['mapIdsToParameterValues'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->never())
                   ->method('mapIdsToParameterValues');

        $result = $repository->findByImageIds($combinationId, []);

        $this->assertSame([], $result);
    }

    /**
     * Tests the clearCombination method.
     * @covers ::clearCombination
     */
    public function testClearCombination(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($this->identicalTo(Icon::class), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('i.combination = :combinationId'))
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
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new IconRepository($this->entityManager);
        $repository->clearCombination($combinationId);
    }
}
