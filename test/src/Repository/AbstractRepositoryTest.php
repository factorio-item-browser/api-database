<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\Common\Test\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\Api\Database\Repository\AbstractRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\AbstractRepository
 */
class AbstractRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @covers ::__construct
     * @throws ReflectionException
     */
    public function testConstruct(): void
    {
        /* @var EntityManagerInterface $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        /* @var AbstractRepository|MockObject $repository */
        $repository = $this->getMockBuilder(AbstractRepository::class)
                           ->setConstructorArgs([$entityManager])
                           ->getMockForAbstractClass();

        $this->assertSame($entityManager, $this->extractProperty($repository, 'entityManager'));
    }
}
