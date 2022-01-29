<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the CachedSearchResultRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository
 */
class CachedSearchResultRepositoryTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return CachedSearchResultRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): CachedSearchResultRepository
    {
        return $this->getMockBuilder(CachedSearchResultRepository::class)
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMock();
    }


    public function testFind(): void
    {
        $locale = 'abc';

        $combinationId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $searchHash = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');
        $queryResult = new CachedSearchResult();

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willReturn($queryResult);

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

        $instance = $this->createInstance();
        $result = $instance->find($combinationId, $locale, $searchHash);

        $this->assertSame($queryResult, $result);
    }

    public function testFindWithException(): void
    {
        $locale = 'abc';

        $combinationId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $searchHash = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willThrowException($this->createMock(NonUniqueResultException::class));

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

        $instance = $this->createInstance();
        $result = $instance->find($combinationId, $locale, $searchHash);

        $this->assertNull($result);
    }

    public function testPersist(): void
    {
        $locale = 'abc';
        $combinationId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $searchHash = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');

        $cachedSearchResult = new CachedSearchResult();
        $cachedSearchResult->setCombinationId($combinationId)
                           ->setLocale($locale)
                           ->setSearchHash($searchHash);

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->identicalTo($cachedSearchResult));
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $instance = $this->createInstance(['find']);
        $instance->expects($this->once())
                 ->method('find')
                 ->with(
                     $this->identicalTo($combinationId),
                     $this->identicalTo($locale),
                     $this->identicalTo($searchHash)
                 )
                 ->willReturn(null);

        $instance->persist($cachedSearchResult);
    }

    public function testPersistWithPersistedEntity(): void
    {
        $locale = 'abc';
        $combinationId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $searchHash = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');

        $cachedSearchResult = new CachedSearchResult();
        $cachedSearchResult->setCombinationId($combinationId)
                           ->setLocale($locale)
                           ->setSearchHash($searchHash);

        $persistedEntity = $this->createMock(CachedSearchResult::class);
        $persistedEntity->expects($this->once())
                        ->method('setLastSearchTime')
                        ->with($this->isInstanceOf(DateTime::class))
                        ->willReturnSelf();

        $this->entityManager->expects($this->never())
                            ->method('persist');
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $instance = $this->createInstance(['find']);
        $instance->expects($this->once())
                 ->method('find')
                 ->with(
                     $this->identicalTo($combinationId),
                     $this->identicalTo($locale),
                     $this->identicalTo($searchHash)
                 )
                 ->willReturn($persistedEntity);

        $instance->persist($cachedSearchResult);
    }

    public function testClearExpiredResults(): void
    {
        $maxAge = new DateTime('2038-01-19T03:14:07');

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

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

        $instance = $this->createInstance();
        $instance->clearExpiredResults($maxAge);
    }

    public function testClearResultsOfCombination(): void
    {
        $combinationId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

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
                         $this->identicalTo(CustomTypes::UUID)
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance();
        $instance->clearResultsOfCombination($combinationId);
    }

    public function testClearAll(): void
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('execute');

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

        $instance = $this->createInstance();
        $instance->clearAll();
    }
}
