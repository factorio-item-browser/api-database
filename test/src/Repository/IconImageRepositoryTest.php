<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use FactorioItemBrowser\Api\Database\Entity\IconData;
use FactorioItemBrowser\Api\Database\Repository\IconImageRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the IconImageRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\IconImageRepository
 */
class IconImageRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    private function createInstance(): IconImageRepository
    {
        return new IconImageRepository(
            $this->entityManager,
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testGetEntityClass(): void
    {
        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'getEntityClass');

        $this->assertSame(IconData::class, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testAddOrphanConditions(): void
    {
        $alias = 'abc';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->once())
                     ->method('leftJoin')
                     ->with($this->identicalTo('abc.icons'), $this->identicalTo('i'))
                     ->willReturnSelf();
        $queryBuilder->expects($this->once())
                     ->method('andWhere')
                     ->with($this->identicalTo('i.image IS NULL'))
                     ->willReturnSelf();

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'addOrphanConditions', $queryBuilder, $alias);
    }
}
