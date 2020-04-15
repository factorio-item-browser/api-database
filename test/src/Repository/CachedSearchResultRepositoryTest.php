<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the CachedSearchResultRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository
 */
class CachedSearchResultRepositoryTest extends TestCase
{
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
     * Tests the find method.
     * @covers ::find
     */
    public function testFind(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);
        /* @var CachedSearchResult&MockObject $queryResult */
        $queryResult = $this->createMock(CachedSearchResult::class);

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('csr')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(CachedSearchResult::class), $this->identicalTo('csr'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('csr.combinationId = :combinationId')],
                         [$this->identicalTo('csr.locale = :locale')],
                         [$this->identicalTo('csr.searchHash = :searchHash')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME),
                         ],
                         [
                             $this->identicalTo('locale'),
                             $this->identicalTo($locale),
                         ],
                         [
                             $this->identicalTo('searchHash'),
                             $this->identicalTo($searchHash),
                             $this->identicalTo(UuidBinaryType::NAME),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new CachedSearchResultRepository($this->entityManager);
        $result = $repository->find($combinationId, $locale, $searchHash);

        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the find method.
     * @covers ::find
     */
    public function testFindWithException(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willThrowException($this->createMock(NonUniqueResultException::class));

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('csr')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(CachedSearchResult::class), $this->identicalTo('csr'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('csr.combinationId = :combinationId')],
                         [$this->identicalTo('csr.locale = :locale')],
                         [$this->identicalTo('csr.searchHash = :searchHash')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(UuidBinaryType::NAME),
                         ],
                         [
                             $this->identicalTo('locale'),
                             $this->identicalTo($locale),
                         ],
                         [
                             $this->identicalTo('searchHash'),
                             $this->identicalTo($searchHash),
                             $this->identicalTo(UuidBinaryType::NAME),
                         ]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new CachedSearchResultRepository($this->entityManager);
        $result = $repository->find($combinationId, $locale, $searchHash);

        $this->assertNull($result);
    }

    /**
     * Tests the persist method.
     * @covers ::persist
     */
    public function testPersist(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        /* @var CachedSearchResult&MockObject $cachedSearchResult */
        $cachedSearchResult = $this->createMock(CachedSearchResult::class);
        $cachedSearchResult->expects($this->once())
                           ->method('getCombinationId')
                           ->willReturn($combinationId);
        $cachedSearchResult->expects($this->once())
                           ->method('getLocale')
                           ->willReturn($locale);
        $cachedSearchResult->expects($this->once())
                           ->method('getSearchHash')
                           ->willReturn($searchHash);

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->identicalTo($cachedSearchResult));
        $this->entityManager->expects($this->once())
                            ->method('flush');

        /* @var CachedSearchResultRepository&MockObject $repository */
        $repository = $this->getMockBuilder(CachedSearchResultRepository::class)
                           ->onlyMethods(['find'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('find')
                   ->with(
                       $this->identicalTo($combinationId),
                       $this->identicalTo($locale),
                       $this->identicalTo($searchHash)
                   )
                   ->willReturn(null);

        $repository->persist($cachedSearchResult);
    }

    /**
     * Tests the persist method.
     * @covers ::persist
     */
    public function testPersistWithPersistedEntity(): void
    {
        $locale = 'abc';

        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);

        /* @var CachedSearchResult&MockObject $cachedSearchResult */
        $cachedSearchResult = $this->createMock(CachedSearchResult::class);
        $cachedSearchResult->expects($this->once())
                           ->method('getCombinationId')
                           ->willReturn($combinationId);
        $cachedSearchResult->expects($this->once())
                           ->method('getLocale')
                           ->willReturn($locale);
        $cachedSearchResult->expects($this->once())
                           ->method('getSearchHash')
                           ->willReturn($searchHash);

        /* @var CachedSearchResult&MockObject $persistedEntity */
        $persistedEntity = $this->createMock(CachedSearchResult::class);
        $persistedEntity->expects($this->once())
                        ->method('setLastSearchTime')
                        ->with($this->isInstanceOf(DateTime::class))
                        ->willReturnSelf();

        $this->entityManager->expects($this->never())
                            ->method('persist');
        $this->entityManager->expects($this->once())
                            ->method('flush');

        /* @var CachedSearchResultRepository&MockObject $repository */
        $repository = $this->getMockBuilder(CachedSearchResultRepository::class)
                           ->onlyMethods(['find'])
                           ->setConstructorArgs([$this->entityManager])
                           ->getMock();
        $repository->expects($this->once())
                   ->method('find')
                   ->with(
                       $this->identicalTo($combinationId),
                       $this->identicalTo($locale),
                       $this->identicalTo($searchHash)
                   )
                   ->willReturn($persistedEntity);

        $repository->persist($cachedSearchResult);
    }

    /**
     * Tests the clearExpiredResults method.
     * @covers ::clearExpiredResults
     */
    public function testClearExpiredResults(): void
    {
        /* @var DateTime&MockObject $maxAge */
        $maxAge = $this->createMock(DateTime::class);

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($this->identicalTo(CachedSearchResult::class), $this->identicalTo('csr'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('csr.lastSearchTime < :maxAge'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('maxAge'),
                         $this->identicalTo($maxAge)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new CachedSearchResultRepository($this->entityManager);
        $repository->clearExpiredResults($maxAge);
    }

    /**
     * Tests the clearResultsOfCombination method.
     * @covers ::clearResultsOfCombination
     */
    public function testClearResultsOfCombination(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);

        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($this->identicalTo(CachedSearchResult::class), $this->identicalTo('csr'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('csr.combinationId = :combinationId'))
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

        $repository = new CachedSearchResultRepository($this->entityManager);
        $repository->clearResultsOfCombination($combinationId);
    }

    /**
     * Tests the clearAll method.
     * @covers ::clearAll
     */
    public function testClearAll(): void
    {
        /* @var AbstractQuery&MockObject $query */
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($this->identicalTo(CachedSearchResult::class), $this->identicalTo('csr'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $repository = new CachedSearchResultRepository($this->entityManager);
        $repository->clearAll();
    }
}
