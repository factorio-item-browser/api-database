<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository\Feature;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByNamesInterface;
use FactorioItemBrowserTestAsset\Api\Database\TestRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * The PHPUnit test of the FindByNamesTrait class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\Feature\FindByNamesTrait
 */
class FindByNamesTraitTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @return FindByNamesInterface<stdClass>
     */
    private function createInstance(): FindByNamesInterface
    {
        return new TestRepository(
            $this->entityManager,
        );
    }

    public function testFindByNames(): void
    {
        $names = ['abc', 'def'];
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $queryResult = [
            $this->createMock(stdClass::class),
            $this->createMock(stdClass::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(stdClass::class), $this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('innerJoin')
                     ->with(
                         $this->identicalTo('e.combinations'),
                         $this->identicalTo('c'),
                         $this->identicalTo('WITH'),
                         $this->identicalTo('c.id = :combinationId')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('e.name IN (:names)'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(2))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('names'),
                             $this->identicalTo($names),
                         ],
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(CustomTypes::UUID),
                         ],
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('getQuery')
                     ->willReturn($query);

        $this->entityManager->expects($this->once())
                            ->method('createQueryBuilder')
                            ->willReturn($queryBuilder);

        $instance = $this->createInstance();
        $result = $instance->findByNames($names, $combinationId);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByNamesWithoutCombinationId(): void
    {
        $names = ['abc', 'def'];

        $queryResult = [
            $this->createMock(stdClass::class),
            $this->createMock(stdClass::class),
        ];

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('getResult')
              ->willReturn($queryResult);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('select')
                     ->with($this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('from')
                     ->with($this->identicalTo(stdClass::class), $this->identicalTo('e'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('e.name IN (:names)'))
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

    public function testFindByNamesWithoutValues(): void
    {
        $names = [];
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance();
        $result = $instance->findByNames($names, $combinationId);

        $this->assertSame([], $result);
    }
}
