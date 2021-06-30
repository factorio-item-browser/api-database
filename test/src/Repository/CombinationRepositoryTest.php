<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use DateTime;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Repository\CombinationRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the CombinationRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\CombinationRepository
 */
class CombinationRepositoryTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return CombinationRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): CombinationRepository
    {
        return $this->getMockBuilder(CombinationRepository::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMock();
    }

    public function testFindById(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);
        $combination = $this->createMock(Combination::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willReturn($combination);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Combination::class), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id = :id'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('id'),
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

        $instance = $this->createInstance();
        $result = $instance->findById($combinationId);

        $this->assertSame($combination, $result);
    }

    public function testFindByIdWithException(): void
    {
        $combinationId = $this->createMock(UuidInterface::class);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getOneOrNullResult')
              ->willThrowException($this->createMock(NonUniqueResultException::class));

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Combination::class), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id = :id'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with(
                         $this->identicalTo('id'),
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

        $instance = $this->createInstance();
        $result = $instance->findById($combinationId);

        $this->assertNull($result);
    }

    public function testFindPossibleCombinationsForUpdate(): void
    {
        $earliestLastUsageTime = $this->createMock(DateTime::class);
        $latestUpdateCheckTime = $this->createMock(DateTime::class);
        $limit = 42;
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
                     ->with($this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(Combination::class), $this->identicalTo('c'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(3))
                     ->method('andWhere')
                     ->withConsecutive(
                         [$this->identicalTo('c.lastUsageTime >= :lastUsageTime')],
                         [$this->identicalTo(
                             '(c.lastUpdateCheckTime IS NULL OR c.lastUpdateCheckTime < :lastUpdateCheckTime',
                         )],
                         [$this->identicalTo('c.lastUsageTime > c.importTime')],
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [$this->identicalTo('lastUsageTime'), $this->identicalTo($earliestLastUsageTime)],
                         [$this->identicalTo('lastUpdateCheckTime'), $this->identicalTo($latestUpdateCheckTime)],
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setMaxResults')
                     ->with($this->identicalTo($limit))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance();
        $result = $instance->findPossibleCombinationsForUpdate($earliestLastUsageTime, $latestUpdateCheckTime, $limit);

        $this->assertSame($queryResult, $result);
    }

    public function testUpdateLastUsageTime(): void
    {
        $combination = $this->createMock(Combination::class);
        $combination->expects($this->once())
                    ->method('setLastUsageTime')
                    ->with($this->isInstanceOf(DateTime::class));

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->identicalTo($combination));
        $this->entityManager->expects($this->once())
                            ->method('flush');

        $instance = $this->createInstance();
        $instance->updateLastUsageTime($combination);
    }

    public function testUpdateLastUsageTimeWithException(): void
    {
        $combination = $this->createMock(Combination::class);
        $combination->expects($this->once())
                    ->method('setLastUsageTime')
                    ->with($this->isInstanceOf(DateTime::class));

        $this->entityManager->expects($this->once())
                            ->method('persist')
                            ->with($this->identicalTo($combination))
                            ->willThrowException($this->createMock(Exception::class));
        $this->entityManager->expects($this->never())
                            ->method('flush');

        $instance = $this->createInstance();
        $instance->updateLastUsageTime($combination);
    }
}
