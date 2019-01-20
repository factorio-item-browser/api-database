<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database;

use FactorioItemBrowser\Api\Database\ConfigProvider;
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
        $configProvider = new ConfigProvider();
        $result = $configProvider();

        $this->assertArrayHasKey('dependencies', $result);
        $this->assertArrayHasKey('factories', $result['dependencies']);

        $this->assertArrayHasKey('doctrine', $result);
        $this->assertArrayHasKey('configuration', $result['doctrine']);
        $this->assertArrayHasKey('driver', $result['doctrine']);
    }
}
