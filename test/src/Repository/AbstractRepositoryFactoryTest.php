<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FactorioItemBrowser\Api\Database\Repository\AbstractRepositoryFactory;
use FactorioItemBrowser\Api\Database\Repository\CachedSearchResultRepository;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the AbstractRepositoryFactory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Repository\AbstractRepositoryFactory
 */
class AbstractRepositoryFactoryTest extends TestCase
{
    /**
     * Provides the data for the __invoke test.
     * @return array
     */
    public function provideInvoke(): array
    {
        return [
            [CachedSearchResultRepository::class],
        ];
    }

    /**
     * Tests the invoking.
     * @param string $requestedName
     * @covers ::__invoke
     * @dataProvider provideInvoke
     */
    public function testInvoke(string $requestedName): void
    {
        /* @var ContainerInterface|MockObject $container */
        $container = $this->getMockBuilder(ContainerInterface::class)
                          ->setMethods(['get'])
                          ->getMockForAbstractClass();
        $container->expects($this->once())
                  ->method('get')
                  ->with(EntityManagerInterface::class)
                  ->willReturn($this->createMock(EntityManagerInterface::class));

        $factory = new AbstractRepositoryFactory();
        $result = $factory($container, $requestedName);
        $this->assertInstanceOf($requestedName, $result);
    }
}
