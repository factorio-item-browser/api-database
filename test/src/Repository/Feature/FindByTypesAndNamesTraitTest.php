<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository\Feature;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesInterface;
use FactorioItemBrowserTestAsset\Api\Database\TestRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * The PHPUnit test of the FindByTypesAndNamesTrait class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\Feature\FindByTypesAndNamesTrait
 */
class FindByTypesAndNamesTraitTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @return FindByTypesAndNamesInterface<stdClass>
     */
    private function createInstance(): FindByTypesAndNamesInterface
    {
        return new TestRepository(
            $this->entityManager,
        );
    }

    public function testFindByTypesAndNames(): void
    {
        $namesByTypes = new NamesByTypes();
        $namesByTypes->setNames('abc', ['def', 'ghi'])
                     ->setNames('jkl', ['mno']);

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
        $queryBuilder->expects($this->exactly(2))
                     ->method('orWhere')
                     ->withConsecutive(
                         [$this->identicalTo('e.type = :type0 AND e.name IN (:names0)')],
                         [$this->identicalTo('e.type = :type1 AND e.name IN (:names1)')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(5))
                     ->method('setParameter')
                     ->withConsecutive(
                         [
                             $this->identicalTo('combinationId'),
                             $this->identicalTo($combinationId),
                             $this->identicalTo(CustomTypes::UUID),
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
        $result = $instance->findByTypesAndNames($namesByTypes, $combinationId);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByTypesAndNamesWithoutCombinationId(): void
    {
        $namesByTypes = new NamesByTypes();
        $namesByTypes->setNames('abc', ['def', 'ghi'])
                     ->setNames('jkl', ['mno']);

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
        $queryBuilder->expects($this->exactly(2))
                     ->method('orWhere')
                     ->withConsecutive(
                         [$this->identicalTo('e.type = :type0 AND e.name IN (:names0)')],
                         [$this->identicalTo('e.type = :type1 AND e.name IN (:names1)')]
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->exactly(4))
                     ->method('setParameter')
                     ->withConsecutive(
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

        $instance = $this->createInstance();
        $result = $instance->findByTypesAndNames($namesByTypes);

        $this->assertSame($queryResult, $result);
    }

    public function testFindByTypesAndNamesWithoutValues(): void
    {
        $namesByTypes = new NamesByTypes();
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $this->entityManager->expects($this->never())
                            ->method('createQueryBuilder');

        $instance = $this->createInstance();
        $result = $instance->findByTypesAndNames($namesByTypes, $combinationId);

        $this->assertSame([], $result);
    }
}
