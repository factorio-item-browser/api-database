<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\RecipeData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

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
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $data = new RecipeData();

        $this->assertSame($data, $data->setId($id));
        $this->assertSame($id, $data->getId());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $data = new RecipeData();

        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }

    /**
     * Tests the setting and getting the mode.
     * @covers ::getMode
     * @covers ::setMode
     */
    public function testSetAndGetMode(): void
    {
        $mode = 'abc';
        $data = new RecipeData();

        $this->assertSame($data, $data->setMode($mode));
        $this->assertSame($mode, $data->getMode());
    }

    /**
     * Tests the setting and getting the item id.
     * @covers ::getItemId
     * @covers ::setItemId
     */
    public function testSetAndGetItemId(): void
    {
        /* @var UuidInterface&MockObject $itemId */
        $itemId = $this->createMock(UuidInterface::class);
        $data = new RecipeData();

        $this->assertSame($data, $data->setItemId($itemId));
        $this->assertSame($itemId, $data->getItemId());
    }
}
