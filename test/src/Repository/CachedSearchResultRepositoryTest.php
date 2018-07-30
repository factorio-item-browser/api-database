<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
     * Provides the data for the findByHash test.
     * @return array
     */
    public function provideFindByHash(): array
    {
        /* @var CachedSearchResult $cachedSearchResult */
        $cachedSearchResult = $this->createMock(CachedSearchResult::class);

        return [
            [false, $cachedSearchResult, $cachedSearchResult],
            [true, $cachedSearchResult, null],
        ];
    }
    /**
     * Tests the findByHash method.
     * @param bool $withException
     * @param mixed $queryResult
     * @param mixed $expectedResult
     * @covers ::findByHash
     * @covers ::getTimeCut
     * @dataProvider provideFindByHash
     */
    public function testFindByHash(bool $withException, $queryResult, $expectedResult)
    {
        $hash = '12ab34cd';

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getOneOrNullResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        if ($withException) {
            $query->expects($this->once())
                  ->method('getOneOrNullResult')
                  ->willThrowException(new NonUniqueResultException());
        } else {
            $query->expects($this->once())
                  ->method('getOneOrNullResult')
                  ->willReturn($queryResult);
        }

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['andWhere', 'setParameter', 'setMaxResults', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->exactly(2))
                     ->method('andWhere')
                     ->withConsecutive(
                         ['r.hash = :hash'],
                         ['r.lastSearchTime > :timeCut']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         ['hash', hex2bin($hash)],
                         ['timeCut', $this->isInstanceOf(DateTime::class)]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with(1)
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var CachedSearchResultRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CachedSearchResultRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('r')
                   ->willReturn($queryBuilder);

        $result = $repository->findByHash($hash);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the cleanup method.
     * @covers ::cleanup
     */
    public function testCleanup()
    {
        $entityName = 'abc';

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['execute'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['delete', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($entityName, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('r.lastSearchTime < :timeCut')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with('timeCut', $this->isInstanceOf(DateTime::class))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var CachedSearchResultRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CachedSearchResultRepository::class)
                           ->setMethods(['createQueryBuilder', 'getEntityName'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('r')
                   ->willReturn($queryBuilder);
        $repository->expects($this->once())
                   ->method('getEntityName')
                   ->willReturn($entityName);

        $result = $repository->cleanup();
        $this->assertSame($repository, $result);
    }

    /**
     * Tests the clear method.
     * @covers ::clear
     */
    public function testClear()
    {
        $entityName = 'abc';

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['execute'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($this->once())
              ->method('execute');

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['delete', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('delete')
                     ->with($entityName, 'r')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var CachedSearchResultRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CachedSearchResultRepository::class)
                           ->setMethods(['createQueryBuilder', 'getEntityName'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('createQueryBuilder')
                   ->with('r')
                   ->willReturn($queryBuilder);
        $repository->expects($this->once())
                   ->method('getEntityName')
                   ->willReturn($entityName);

        $result = $repository->clear();
        $this->assertSame($repository, $result);
    }
}
