<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\IconImage;
use FactorioItemBrowser\Api\Database\Repository\IconImageRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the IconImageRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\IconImageRepository
 */
class IconImageRepositoryTest extends TestCase
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
        $repository = new IconImageRepository($this->entityManager);
        $result = $this->invokeMethod($repository, 'getEntityClass');

        $this->assertSame(IconImage::class, $result);
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
                     ->with($this->identicalTo('abc.icons'), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('i.image IS NULL'))
                     ->willReturnSelf();

        $repository = new IconImageRepository($this->entityManager);
        $this->invokeMethod($repository, 'addOrphanConditions', $queryBuilder, $alias);
    }
}
