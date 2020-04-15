<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Machine class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Machine
 */
class MachineTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getCombinations
     * @covers ::getCraftingCategories
     */
    public function testConstruct(): void
    {
        $machine = new Machine();

        $this->assertInstanceOf(ArrayCollection::class, $machine->getCombinations());
        $this->assertInstanceOf(ArrayCollection::class, $machine->getCraftingCategories());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $machine = new Machine();

        $this->assertSame($machine, $machine->setId($id));
        $this->assertSame($id, $machine->getId());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $machine = new Machine();

        $this->assertSame($machine, $machine->setName($name));
        $this->assertSame($name, $machine->getName());
    }

    /**
     * Tests setting and getting the craftingSpeed.
     * @covers ::getCraftingSpeed
     * @covers ::setCraftingSpeed
     */
    public function testSetAndGetCraftingSpeed(): void
    {
        $craftingSpeed = 13.37;
        $machine = new Machine();

        $this->assertSame($machine, $machine->setCraftingSpeed($craftingSpeed));
        $this->assertSame($craftingSpeed, $machine->getCraftingSpeed());
    }


    /**
     * Tests setting and getting the numberOfItemSlots.
     * @covers ::getNumberOfItemSlots
     * @covers ::setNumberOfItemSlots
     */
    public function testSetAndGetNumberOfItemSlots(): void
    {
        $numberOfItemSlots = 42;
        $machine = new Machine();

        $this->assertSame($machine, $machine->setNumberOfItemSlots($numberOfItemSlots));
        $this->assertSame($numberOfItemSlots, $machine->getNumberOfItemSlots());
    }

    /**
     * Tests setting and getting the numberOfFluidInputSlots.
     * @covers ::getNumberOfFluidInputSlots
     * @covers ::setNumberOfFluidInputSlots
     */
    public function testSetAndGetNumberOfFluidInputSlots(): void
    {
        $numberOfFluidInputSlots = 42;
        $machine = new Machine();

        $this->assertSame($machine, $machine->setNumberOfFluidInputSlots($numberOfFluidInputSlots));
        $this->assertSame($numberOfFluidInputSlots, $machine->getNumberOfFluidInputSlots());
    }

    /**
     * Tests setting and getting the numberOfFluidOutputSlots.
     * @covers ::getNumberOfFluidOutputSlots
     * @covers ::setNumberOfFluidOutputSlots
     */
    public function testSetAndGetNumberOfFluidOutputSlots(): void
    {
        $numberOfFluidOutputSlots = 42;
        $machine = new Machine();

        $this->assertSame($machine, $machine->setNumberOfFluidOutputSlots($numberOfFluidOutputSlots));
        $this->assertSame($numberOfFluidOutputSlots, $machine->getNumberOfFluidOutputSlots());
    }

    /**
     * Tests setting and getting the numberOfModuleSlots.
     * @covers ::getNumberOfModuleSlots
     * @covers ::setNumberOfModuleSlots
     */
    public function testSetAndGetNumberOfModuleSlots(): void
    {
        $numberOfModuleSlots = 42;
        $machine = new Machine();

        $this->assertSame($machine, $machine->setNumberOfModuleSlots($numberOfModuleSlots));
        $this->assertSame($numberOfModuleSlots, $machine->getNumberOfModuleSlots());
    }

    /**
     * Tests setting and getting the energyUsage.
     * @covers ::getEnergyUsage
     * @covers ::setEnergyUsage
     */
    public function testSetAndGetEnergyUsage(): void
    {
        $energyUsage = 13.37;
        $machine = new Machine();

        $this->assertSame($machine, $machine->setEnergyUsage($energyUsage));
        $this->assertSame($energyUsage, $machine->getEnergyUsage());
    }

    /**
     * Tests setting and getting the energyUsageUnit.
     * @covers ::getEnergyUsageUnit
     * @covers ::setEnergyUsageUnit
     */
    public function testSetAndGetEnergyUsageUnit(): void
    {
        $energyUsageUnit = 'abc';
        $machine = new Machine();

        $this->assertSame($machine, $machine->setEnergyUsageUnit($energyUsageUnit));
        $this->assertSame($energyUsageUnit, $machine->getEnergyUsageUnit());
    }
}
