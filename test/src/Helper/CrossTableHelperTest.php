<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\UnexpectedResultException;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Entity\EntityWithId;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Helper\CrossTableHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the CrossTableHelper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Helper\CrossTableHelper
 */
class CrossTableHelperTest extends TestCase
{
    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;
    private string $crossTableName = 'cross';
    private string $combinationColumnName = 'combinationId';
    private string $entityColumnName = 'entityId';

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @return CrossTableHelper<EntityWithId>
     */
    private function createInstance(): CrossTableHelper
    {
        return new CrossTableHelper(
            $this->entityManager,
            $this->crossTableName,
            $this->combinationColumnName,
            $this->entityColumnName,
        );
    }

    public function testClear(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $combination = new Combination();
        $combination->setId($combinationId);

        $expectedSql = 'DELETE FROM cross WHERE combinationId = :combinationId';

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('combinationId'),
                  $this->identicalTo($combinationId),
                  $this->identicalTo(CustomTypes::UUID),
              );
        $query->expects($this->once())
              ->method('execute');

        $this->entityManager->expects($this->once())
                            ->method('createNativeQuery')
                            ->with($this->identicalTo($expectedSql), $this->isInstanceOf(ResultSetMapping::class))
                            ->willReturn($query);

        $instance = $this->createInstance();
        $instance->clear($combination);
    }

    public function testInsert(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $combination = new Combination();
        $combination->setId($combinationId);

        $id1 = Uuid::fromString('11c9b237-8049-4671-a6a1-7d79ddbb1552');
        $id2 = Uuid::fromString('22043e4e-093d-441a-a6fb-61420c5106f2');
        $id3 = Uuid::fromString('3551eaf4-c742-4fd7-9461-38b6680f7061');

        $entity1 = new Item();
        $entity1->setId($id1);
        $entity2 = new Item();
        $entity2->setId($id2);
        $entity3 = new Item();
        $entity3->setId($id3);
        $entities = [$entity1, $entity2, $entity3];

        $expectedSql = 'INSERT IGNORE INTO cross (combinationId, entityId) VALUES (?,?),(?,?),(?,?)';
        $expectedParameters = new ArrayCollection([
            new Parameter('0', $combinationId, CustomTypes::UUID),
            new Parameter('1', $id1, CustomTypes::UUID),
            new Parameter('2', $combinationId, CustomTypes::UUID),
            new Parameter('3', $id2, CustomTypes::UUID),
            new Parameter('4', $combinationId, CustomTypes::UUID),
            new Parameter('5', $id3, CustomTypes::UUID),
        ]);

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameters')
              ->with($this->equalTo($expectedParameters));
        $query->expects($this->once())
              ->method('execute');

        $this->entityManager->expects($this->once())
                            ->method('createNativeQuery')
                            ->with($this->identicalTo($expectedSql), $this->isInstanceOf(ResultSetMapping::class))
                            ->willReturn($query);

        $instance = $this->createInstance();
        $instance->insert($combination, $entities);
    }

    public function testInsertWithoutEntities(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $combination = new Combination();
        $combination->setId($combinationId);

        $this->entityManager->expects($this->never())
                            ->method('createNativeQuery');

        $instance = $this->createInstance();
        $instance->insert($combination, []);
    }

    public function testCount(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $combination = new Combination();
        $combination->setId($combinationId);
        $count = 42;

        $expectedSql = 'SELECT COUNT(1) AS c FROM cross WHERE combinationId = :combinationId';

        $expectedResultSetMapping = new ResultSetMapping();
        $expectedResultSetMapping->addScalarResult('c', 'c');

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('combinationId'),
                  $this->identicalTo($combinationId),
                  $this->identicalTo(CustomTypes::UUID),
              );
        $query->expects($this->once())
              ->method('getSingleScalarResult')
              ->willReturn($count);

        $this->entityManager->expects($this->once())
                            ->method('createNativeQuery')
                            ->with($this->identicalTo($expectedSql), $this->equalTo($expectedResultSetMapping))
                            ->willReturn($query);

        $instance = $this->createInstance();
        $result = $instance->count($combination);

        $this->assertSame($count, $result);
    }

    public function testCountWithException(): void
    {
        $combinationId = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');
        $combination = new Combination();
        $combination->setId($combinationId);

        $expectedSql = 'SELECT COUNT(1) AS c FROM cross WHERE combinationId = :combinationId';

        $expectedResultSetMapping = new ResultSetMapping();
        $expectedResultSetMapping->addScalarResult('c', 'c');

        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())
              ->method('setParameter')
              ->with(
                  $this->identicalTo('combinationId'),
                  $this->identicalTo($combinationId),
                  $this->identicalTo(CustomTypes::UUID),
              );
        $query->expects($this->once())
              ->method('getSingleScalarResult')
              ->willThrowException($this->createMock(UnexpectedResultException::class));

        $this->entityManager->expects($this->once())
                            ->method('createNativeQuery')
                            ->with($this->identicalTo($expectedSql), $this->equalTo($expectedResultSetMapping))
                            ->willReturn($query);

        $instance = $this->createInstance();
        $result = $instance->count($combination);

        $this->assertSame(0, $result);
    }
}
