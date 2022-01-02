<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\Api\Database\Repository\AbstractRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractRepository class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Repository\AbstractRepository
 */
class AbstractRepositoryTest extends TestCase
{
    use ReflectionTrait;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return AbstractRepository&MockObject
     */
    private function createInstance(array $mockedMethods = []): AbstractRepository
    {
        return $this->getMockBuilder(AbstractRepository::class)
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->entityManager,
                    ])
                    ->getMockForAbstractClass();
    }


    /**
     * @throws ReflectionException
     */
    public function testMapIdsToParameterValues(): void
    {
        $id1 = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $id2 = Uuid::fromString('fedcba98-7654-3210-fedc-ba9876543210');
        $expectedResult = [$id1->getBytes(), $id2->getBytes()];

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'mapIdsToParameterValues', [$id1, $id2]);

        $this->assertSame($expectedResult, $result);
    }
}
