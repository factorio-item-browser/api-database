<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\MachineData;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the MachineData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Data\MachineData
 */
class MachineDataTest extends TestCase
{
    /**
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $data = new MachineData();

        $this->assertSame(0, $data->getId());
        $this->assertSame('', $data->getName());
        $this->assertSame(0, $data->getOrder());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $data = new MachineData();

        $id = 42;
        $this->assertSame($data, $data->setId($id));
        $this->assertSame($id, $data->getId());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $data = new MachineData();

        $name = 'abc';
        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }

    /**
     * Tests setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder(): void
    {
        $data = new MachineData();

        $order = 42;
        $this->assertSame($data, $data->setOrder($order));
        $this->assertSame($order, $data->getOrder());
    }

    /**
     * Tests the getKeys method.
     * @covers ::getKeys
     */
    public function testGetKeys(): void
    {
        $data = new MachineData();
        $data->setName('abc');
        $expectedResult = ['abc'];

        $result = $data->getKeys();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the createFromArray method.
     * @covers ::createFromArray
     */
    public function testCreateFromArray(): void
    {
        $array = [
            'id' => 42,
            'name' => 'abc',
            'order' => 1337,
        ];

        $data = MachineData::createFromArray($array);
        $this->assertSame(42, $data->getId());
        $this->assertSame('abc', $data->getName());
        $this->assertSame(1337, $data->getOrder());
    }
}
