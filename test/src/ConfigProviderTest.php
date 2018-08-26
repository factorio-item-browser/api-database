<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database;

use FactorioItemBrowser\Api\Database\ConfigProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ConfigProvider class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\ConfigProvider
 */
class ConfigProviderTest extends TestCase
{
    /**
     * Tests the invoking.
     * @covers ::__invoke
     */
    public function testInvoke(): void
    {
        $dependencyConfig = ['abc' => 'def'];
        $doctrineConfig = ['ghi' => 'jkl'];
        $expectedResult = [
            'dependencies' => ['abc' => 'def'],
            'doctrine' => ['ghi' => 'jkl'],
        ];

        /* @var ConfigProvider|MockObject $configProvider */
        $configProvider = $this->getMockBuilder(ConfigProvider::class)
                               ->setMethods(['getDependencyConfig', 'getDoctrineConfig'])
                               ->disableOriginalConstructor()
                               ->getMock();
        $configProvider->expects($this->once())
                       ->method('getDependencyConfig')
                       ->willReturn($dependencyConfig);
        $configProvider->expects($this->once())
                       ->method('getDoctrineConfig')
                       ->willReturn($doctrineConfig);

        $result = $configProvider();
        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getDependencyConfig method.
     * @covers ::getDependencyConfig
     */
    public function testGetDependencyConfig(): void
    {
        $expectedKeys = [
            'factories',
        ];

        $configProvider = new ConfigProvider();
        $result = $configProvider->getDependencyConfig();
        foreach ($expectedKeys as $expectedKey) {
            $this->assertArrayHasKey($expectedKey, $result);
        }
    }

    /**
     * Tests the getDoctrineConfig method.
     * @covers ::getDoctrineConfig
     */
    public function testGetDoctrineConfig(): void
    {
        $expectedKeys = [
            'configuration',
            'driver'
        ];

        $configProvider = new ConfigProvider();
        $result = $configProvider->getDoctrineConfig();
        foreach ($expectedKeys as $expectedKey) {
            $this->assertArrayHasKey($expectedKey, $result);
        }
    }
}
