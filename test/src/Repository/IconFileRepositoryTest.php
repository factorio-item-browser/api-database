<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\IconImage;
use FactorioItemBrowser\Api\Database\Repository\IconImageRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the IconFileRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\IconImageRepository
 */
class IconFileRepositoryTest extends TestCase
{
    use ReflectionTrait;
    
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
     * @throws ReflectionException
     * @covers ::findByHashes
     * @dataProvider provideFindByHashes
     */
    public function testFindByHashes(bool $withHashes): void
    {
        $hashes = $withHashes ? ['ab12cd34', '12ab34cd'] : [];
        $expectedHashes = $withHashes ? [hex2bin('ab12cd34'), hex2bin('12ab34cd')] : [];
        $queryResult = $withHashes ? [$this->createMock(IconImage::class)] : [];

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
                             ->setMethods(['select', 'from', 'andWhere', 'setParameter', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('select')
                     ->with('if')
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('from')
                     ->with(IconImage::class, 'if')
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('andWhere')
                     ->with('if.hash IN (:hashes)')
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('setParameter')
                     ->with('hashes', $expectedHashes)
                     ->willReturnSelf();
        $queryBuilder->expects($withHashes ? $this->once() : $this->never())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($withHashes ? $this->once() : $this->never())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new IconImageRepository($entityManager);

        $result = $repository->findByHashes($hashes);
        $this->assertSame($queryResult, $result);
    }
    
    /**
     * Provides the data for the removeOrphans test.
     * @return array
     */
    public function provideRemoveOrphans(): array
    {
        return [
            [['ab12cd34', '12ab34cd'], true],
            [[], false],
        ];
    }

    /**
     * Tests the removeOrphans method.
     * @param array $orphanedHashes
     * @param bool $expectRemove
     * @throws ReflectionException
     * @covers ::removeOrphans
     * @dataProvider provideRemoveOrphans
     */
    public function testRemoveOrphans(array $orphanedHashes, bool $expectRemove): void
    {
        /* @var IconImageRepository|MockObject $repository */
        $repository = $this->getMockBuilder(IconImageRepository::class)
                           ->setMethods(['findOrphanedHashes', 'removeHashes'])
                           ->disableOriginalConstructor()
                           ->getMock();
        $repository->expects($this->once())
                   ->method('findOrphanedHashes')
                   ->willReturn($orphanedHashes);
        $repository->expects($expectRemove ? $this->once() : $this->never())
                   ->method('removeHashes')
                   ->with($orphanedHashes);

        $repository->removeOrphans();
    }

    /**
     * Tests the findOrphanedHashes method.
     * @throws ReflectionException
     * @covers ::findOrphanedHashes
     */
    public function testFindOrphanedHashes(): void
    {
        $queryResult = [
            ['hash' => 'ab12cd34'],
            ['hash' => '12ab34cd']
        ];
        $expectedResult = ['ab12cd34', '12ab34cd'];

        /* @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
                      ->setMethods(['getResult'])
                      ->disableOriginalConstructor()
                      ->getMockForAbstractClass();
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        /* @var QueryBuilder|MockObject $queryBuilder */
        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
                             ->setMethods(['select', 'from', 'leftJoin', 'andWhere', 'getQuery'])
                             ->disableOriginalConstructor()
                             ->getMock();
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with('if.hash AS hash')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with(IconImage::class, 'if')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with('if.icons', 'i')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('i.id IS NULL')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new IconImageRepository($entityManager);

        $result = $this->invokeMethod($repository, 'findOrphanedHashes');
        $this->assertEquals($expectedResult, $result);
    }
    
    /**
     * Tests the removeHashes method.
     * @throws ReflectionException
     * @covers ::removeHashes
     */
    public function testRemoveHashes(): void
    {
        $hashes = ['ab12cd34', '12ab34cd'];

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
                     ->with(IconImage::class, 'if')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with('if.hash IN (:hashes)')
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('setParameter')
                     ->with('hashes', $hashes)
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        /* @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->getMockBuilder(EntityManagerInterface::class)
                              ->setMethods(['createQueryBuilder'])
                              ->getMockForAbstractClass();
        $entityManager->expects($this->once())
                      ->method('createQueryBuilder')
                      ->willReturn($queryBuilder);

        $repository = new IconImageRepository($entityManager);

        $this->invokeMethod($repository, 'removeHashes', $hashes);
    }
}
