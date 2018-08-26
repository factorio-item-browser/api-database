<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\IconData;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the IconData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Data\IconData
 */
class IconDataTest extends TestCase
{
    /**
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $data = new IconData();

        $this->assertSame(0, $data->getId());
        $this->assertSame('', $data->getHash());
        $this->assertSame('', $data->getType());
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
        $data = new IconData();

        $id = 42;
        $this->assertSame($data, $data->setId($id));
        $this->assertSame($id, $data->getId());
    }

    /**
     * Tests setting and getting the hash.
     * @covers ::getHash
     * @covers ::setHash
     */
    public function testSetAndGetHash(): void
    {
        $data = new IconData();

        $hash = 'ab12cd34';
        $this->assertSame($data, $data->setHash($hash));
        $this->assertSame($hash, $data->getHash());
    }

    /**
     * Tests setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $data = new IconData();

        $type = 'abc';
        $this->assertSame($data, $data->setType($type));
        $this->assertSame($type, $data->getType());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $data = new IconData();

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
        $data = new IconData();

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
        $data = new IconData();
        $data->setType('abc')
             ->setName('def');
        $expectedResult = ['abc', 'def'];

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
            'hash' => hex2bin('ab12cd34'),
            'type' => 'abc',
            'name' => 'def',
            'order' => 1337,
        ];

        $data = IconData::createFromArray($array);
        $this->assertSame(42, $data->getId());
        $this->assertSame('ab12cd34', $data->getHash());
        $this->assertSame('abc', $data->getType());
        $this->assertSame('def', $data->getName());
        $this->assertSame(1337, $data->getOrder());
    }
}
