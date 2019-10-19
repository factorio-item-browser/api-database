<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Repository\ModRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ModRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\ModRepository
 */
class ModRepositoryTest extends TestCase
{
    use ReflectionTrait;

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
     * Tests the getEntityClass method.
     * @throws ReflectionException
     * @covers ::getEntityClass
     */
    public function testGetEntityClass(): void
    {
        $repository = new ModRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(Mod::class, $result);
    }

    /**
     * Tests the addOrphanConditions method.
     * @throws ReflectionException
     * @covers ::addOrphanConditions
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        /* @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with(
                         $this->identicalTo('abc.combinations'),
                         $this->identicalTo('c')
                     )
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('c.id IS NULL'))
                     ->willReturnSelf();

        $repository = new ModRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }
}
