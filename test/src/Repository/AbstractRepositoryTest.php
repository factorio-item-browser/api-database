<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\Api\Database\Repository\AbstractRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
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
     * Tests the constructing.
     * @covers ::__construct
     * @throws ReflectionException
     */
    public function testConstruct(): void
    {
        /* @var AbstractRepository&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractRepository::class)
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();

        $this->assertSame($this->entityManager, $this->extractProperty($repository, 'entityManager'));
    }

    /**
     * Tests the mapIdsToParameterValues method.
     * @throws ReflectionException
     * @covers ::mapIdsToParameterValues
     */
    public function testMapIdsToParameterValues(): void
    {
        $expectedResult = ['abc', 'def'];

        /* @var UuidInterface&MockObject $id1 */
        $id1 = $this->createMock(UuidInterface::class);
        $id1->expects($this->once())
            ->method('getBytes')
            ->willReturn('abc');

        /* @var UuidInterface&MockObject $id2 */
        $id2 = $this->createMock(UuidInterface::class);
        $id2->expects($this->once())
            ->method('getBytes')
            ->willReturn('def');

        /* @var AbstractRepository&MockObject $repository */
        $repository = $this->getMockBuilder(AbstractRepository::class)
                           ->setConstructorArgs([$this->entityManager])
                           ->getMockForAbstractClass();

        $result = $this->invokeMethod($repository, 'mapIdsToParameterValues', [$id1, $id2]);

        $this->assertSame($expectedResult, $result);
    }
}
