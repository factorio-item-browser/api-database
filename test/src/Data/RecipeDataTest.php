<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\RecipeData;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Data\RecipeData
 */
class RecipeDataTest extends TestCase
{
    /**
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $data = new RecipeData();

        $this->assertSame(0, $data->getId());
        $this->assertSame('', $data->getName());
        $this->assertSame('', $data->getMode());
        $this->assertSame(0, $data->getItemId());
        $this->assertSame(0, $data->getOrder());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $data = new RecipeData();

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
        $data = new RecipeData();

        $name = 'abc';
        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }

    /**
     * Tests setting and getting the mode.
     * @covers ::getMode
     * @covers ::setMode
     */
    public function testSetAndGetMode(): void
    {
        $data = new RecipeData();

        $mode = 'abc';
        $this->assertSame($data, $data->setMode($mode));
        $this->assertSame($mode, $data->getMode());
    }

    /**
     * Tests setting and getting the itemId.
     * @covers ::getItemId
     * @covers ::setItemId
     */
    public function testSetAndGetItemId(): void
    {
        $data = new RecipeData();

        $itemId = 42;
        $this->assertSame($data, $data->setItemId($itemId));
        $this->assertSame($itemId, $data->getItemId());
    }

    /**
     * Tests setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder(): void
    {
        $data = new RecipeData();

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
        $data = new RecipeData();
        $data->setName('abc')
             ->setMode('def')
             ->setItemId(42);
        $expectedResult = ['abc', 'def', '42'];

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
            'mode' => 'def',
            'itemId' => 21,
            'order' => 1337,
        ];

        $data = RecipeData::createFromArray($array);
        $this->assertSame(42, $data->getId());
        $this->assertSame('abc', $data->getName());
        $this->assertSame('def', $data->getMode());
        $this->assertSame(21, $data->getItemId());
        $this->assertSame(1337, $data->getOrder());
    }
}
