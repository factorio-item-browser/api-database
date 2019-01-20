<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use DateTime;
use Doctrine\ORM\AbstractQuery;
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
     * Provides the data for the findByHashes test.
     * @return array
     */
    public function provideFindByHashes(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * Tests the findByHashes method.
     * @param bool $withHashes
     * @covers ::findByHashes
     * @dataProvider provideFindByHashes
     */
    public function testFindByHashes(bool $withHashes): void
    {
        $hashes = $withHashes ? ['ab12cd34', '12ab34cd'] : [];
        $expectedHashes = $withHashes ? [hex2bin('ab12cd34'), hex2bin('12ab34cd')] : [];
        $queryResult = $withHashes ? [$this->createMock(CachedSearchResult::class)] : [];
        $maxAge = new DateTime('2038-01-19 03:14:07');

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($withHashes ? $this->once() : $this->never())
              ->method('getResult')
              ->willReturn($queryResult);


        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withHashes ? $this->exactly(2) : $this->never())
                     ->method('andWhere')
                     ->withConsecutive(
                         ['r.hash IN (:hashes)'],
                         ['r.lastSearchTime > :maxAge']
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->exactly(2) : $this->never())
                     ->method('setParameter')
                     ->withConsecutive(
                         ['hashes', $expectedHashes],
                         ['maxAge', $maxAge]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var CachedSearchResultRepository|MockObject $repository */
        $repository = $this->getMockBuilder(CachedSearchResultRepository::class)
                           ->setMethods(['createQueryBuilder'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($withHashes ? $this->once() : $this->never())
                   ->method('createQueryBuilder')
                   ->with('r')
                   ->willReturn($queryBuilder);

        $result = $repository->findByHashes($hashes, $maxAge);
        $this->assertSame($queryResult, $result);
    }

    /**
     * Tests the cleanup method.
     * @covers ::cleanup
     */
    public function testCleanup(): void
    {
        $entityName = 'abc';
        $maxAge = new DateTime('2038-01-19 03:14:07');

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
                     ->with('r.lastSearchTime < :maxAge')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with('maxAge', $maxAge)
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

        $repository->cleanup($maxAge);
    }

    /**
     * Tests the clear method.
     * @covers ::clear
     */
    public function testClear(): void
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

        $repository->clear();
    }
}
