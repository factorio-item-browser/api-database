<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use PHPUnit\Framework\TestCase;

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
     * @covers ::getModCombinations
     * @covers ::getCraftingCategories
     */
    public function testConstruct(): void
    {
        $name = 'abc';
        $machine = new Machine($name);

        $this->assertSame(0, $machine->getId());
        $this->assertSame($name, $machine->getName());
        $this->assertInstanceOf(ArrayCollection::class, $machine->getModCombinations());
        $this->assertInstanceOf(ArrayCollection::class, $machine->getCraftingCategories());
        $this->assertSame(1., $machine->getCraftingSpeed());
        $this->assertSame(0, $machine->getNumberOfItemSlots());
        $this->assertSame(0, $machine->getNumberOfFluidInputSlots());
        $this->assertSame(0, $machine->getNumberOfFluidOutputSlots());
        $this->assertSame(0, $machine->getNumberOfModuleSlots());
        $this->assertSame(0., $machine->getEnergyUsage());
        $this->assertSame('', $machine->getEnergyUsageUnit());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $machine = new Machine('foo');

        $id = 42;
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
        $machine = new Machine('foo');

        $name = 'abc';
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
        $machine = new Machine('foo');

        $craftingSpeed = 13.37;
        $this->assertSame($machine, $machine->setCraftingSpeed($craftingSpeed));
        $this->assertSame($craftingSpeed, $machine->getCraftingSpeed());
    }

    /**
     * Provides the data for the setAndGetNumberOfItems test.
     * @return array
     */
    public function provideSetAndGetNumberOfItems(): array
    {
        return [
            [32],
            [Machine::VALUE_UNLIMITED_SLOTS],
        ];
    }

    /**
     * Tests setting and getting the numberOfItemSlots.
     * @param int $numberOfItemSlots
     * @covers ::getNumberOfItemSlots
     * @covers ::setNumberOfItemSlots
     * @dataProvider provideSetAndGetNumberOfItems
     */
    public function testSetAndGetNumberOfItemSlots($numberOfItemSlots): void
    {
        $machine = new Machine('foo');

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
        $machine = new Machine('foo');

        $numberOfFluidInputSlots = 42;
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
        $machine = new Machine('foo');

        $numberOfFluidOutputSlots = 42;
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
        $machine = new Machine('foo');

        $numberOfModuleSlots = 42;
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
        $machine = new Machine('foo');

        $energyUsage = 13.37;
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
        $machine = new Machine('foo');

        $energyUsageUnit = 'abc';
        $this->assertSame($machine, $machine->setEnergyUsageUnit($energyUsageUnit));
        $this->assertSame($energyUsageUnit, $machine->getEnergyUsageUnit());
    }
}
