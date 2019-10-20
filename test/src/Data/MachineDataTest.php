<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\MachineData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

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
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $data = new MachineData();

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
        $data = new MachineData();

        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }
}
