<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Category;
use FactorioItemBrowser\Api\Database\Entity\Machine;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
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
    public function test(): void
    {
        $id = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $name = 'abc';
        $speed = 13.37;
        $numberOfItemSlots = 12;
        $numberOfFluidInputSlots = 34;
        $numberOfFluidOutputSlots = 56;
        $numberOfModuleSlots = 78;
        $energyUsage = 73.31;
        $energyUsageUnit = 'def';

        $instance = new Machine();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCategories());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());

        $this->assertSame($instance, $instance->setSpeed($speed));
        $this->assertSame($speed, $instance->getSpeed());

        $this->assertSame($instance, $instance->setNumberOfItemSlots($numberOfItemSlots));
        $this->assertSame($numberOfItemSlots, $instance->getNumberOfItemSlots());

        $this->assertSame($instance, $instance->setNumberOfFluidInputSlots($numberOfFluidInputSlots));
        $this->assertSame($numberOfFluidInputSlots, $instance->getNumberOfFluidInputSlots());

        $this->assertSame($instance, $instance->setNumberOfFluidOutputSlots($numberOfFluidOutputSlots));
        $this->assertSame($numberOfFluidOutputSlots, $instance->getNumberOfFluidOutputSlots());

        $this->assertSame($instance, $instance->setNumberOfModuleSlots($numberOfModuleSlots));
        $this->assertSame($numberOfModuleSlots, $instance->getNumberOfModuleSlots());

        $this->assertSame($instance, $instance->setEnergyUsage($energyUsage));
        $this->assertSame($energyUsage, $instance->getEnergyUsage());

        $this->assertSame($instance, $instance->setEnergyUsageUnit($energyUsageUnit));
        $this->assertSame($energyUsageUnit, $instance->getEnergyUsageUnit());
    }

    public function testIdCalculation(): void
    {
        $category1 = new Category();
        $category1->setType('abc')
                  ->setName('def');

        $category2 = new Category();
        $category2->setType('ghi')
                  ->setName('jkl');

        $instance = new Machine();
        $instance->setId(Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450'))
                 ->setName('abc')
                 ->setSpeed(13.37)
                 ->setNumberOfItemSlots(12)
                 ->setNumberOfFluidInputSlots(34)
                 ->setNumberOfFluidOutputSlots(56)
                 ->setNumberOfModuleSlots(78)
                 ->setEnergyUsage(73.31)
                 ->setEnergyUsageUnit('def');
        $instance->getCategories()->add($category1);
        $instance->getCategories()->add($category2);

        $expectedId = '70d915cd-b6fb-dbc0-f2ba-e7cf7caca827';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
