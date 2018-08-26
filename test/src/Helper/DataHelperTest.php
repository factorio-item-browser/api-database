<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Helper;

use FactorioItemBrowser\Api\Database\Helper\DataHelper;
use FactorioItemBrowserTestAsset\Api\Database\Data\TestData;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the DataHelper class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Helper\DataHelper
 */
class DataHelperTest extends TestCase
{
    /**
     * Tests the filter method.
     * @covers ::filter
     */
    public function testFilter(): void
    {
        $data1 = new TestData(42, ['abc']);
        $data2 = new TestData(21, ['abc']);
        $data3 = new TestData(42, ['def']);
        $data4 = new TestData(1337, ['ghi']);
        $data5 = new TestData(7331, ['ghi']);

        $expectedResult = [$data1, $data3, $data5];

        $helper = new DataHelper();
        $result = $helper->filter([$data1, $data2, $data3, $data4, $data5]);
        $this->assertEquals($expectedResult, $result);
    }
}
