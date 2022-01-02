<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Machine class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Machine
 */
class MachineTest extends TestCase
{
    private function createInstance(): Machine
    {
        return new Machine();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getCraftingCategories());
    }

    public function testSetAndGetId(): void
    {
        $id = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }

    public function testSetAndGetCraftingSpeed(): void
    {
        $craftingSpeed = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCraftingSpeed($craftingSpeed));
        $this->assertSame($craftingSpeed, $instance->getCraftingSpeed());
    }


    public function testSetAndGetNumberOfItemSlots(): void
    {
        $numberOfItemSlots = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfItemSlots($numberOfItemSlots));
        $this->assertSame($numberOfItemSlots, $instance->getNumberOfItemSlots());
    }

    public function testSetAndGetNumberOfFluidInputSlots(): void
    {
        $numberOfFluidInputSlots = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfFluidInputSlots($numberOfFluidInputSlots));
        $this->assertSame($numberOfFluidInputSlots, $instance->getNumberOfFluidInputSlots());
    }

    public function testSetAndGetNumberOfFluidOutputSlots(): void
    {
        $numberOfFluidOutputSlots = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfFluidOutputSlots($numberOfFluidOutputSlots));
        $this->assertSame($numberOfFluidOutputSlots, $instance->getNumberOfFluidOutputSlots());
    }

    public function testSetAndGetNumberOfModuleSlots(): void
    {
        $numberOfModuleSlots = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfModuleSlots($numberOfModuleSlots));
        $this->assertSame($numberOfModuleSlots, $instance->getNumberOfModuleSlots());
    }

    public function testSetAndGetEnergyUsage(): void
    {
        $energyUsage = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setEnergyUsage($energyUsage));
        $this->assertSame($energyUsage, $instance->getEnergyUsage());
    }

    public function testSetAndGetEnergyUsageUnit(): void
    {
        $energyUsageUnit = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setEnergyUsageUnit($energyUsageUnit));
        $this->assertSame($energyUsageUnit, $instance->getEnergyUsageUnit());
    }
}
