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
    private function createInstance(): Machine
    {
        return new Machine();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCategories());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testName(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($value));
        $this->assertSame($value, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testSpeed(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setSpeed($value));
        $this->assertSame($value, $instance->getSpeed());

        $this->assertSame(4294967.295, $instance->setSpeed(123456789.123465)->getSpeed());
    }

    public function testNumberOfItemSlots(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfItemSlots($value));
        $this->assertSame($value, $instance->getNumberOfItemSlots());

        $this->assertSame(255, $instance->setNumberOfItemSlots(1337)->getNumberOfItemSlots());
    }

    public function testNumberOfFluidInputSlots(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfFluidInputSlots($value));
        $this->assertSame($value, $instance->getNumberOfFluidInputSlots());

        $this->assertSame(255, $instance->setNumberOfFluidInputSlots(1337)->getNumberOfFluidInputSlots());
    }

    public function testNumberOfFluidOutputSlots(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfFluidOutputSlots($value));
        $this->assertSame($value, $instance->getNumberOfFluidOutputSlots());

        $this->assertSame(255, $instance->setNumberOfFluidOutputSlots(1337)->getNumberOfFluidOutputSlots());
    }

    public function testNumberOfModuleSlots(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNumberOfModuleSlots($value));
        $this->assertSame($value, $instance->getNumberOfModuleSlots());

        $this->assertSame(255, $instance->setNumberOfModuleSlots(1337)->getNumberOfModuleSlots());
    }

    public function testEnergyUsage(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setEnergyUsage($value));
        $this->assertSame($value, $instance->getEnergyUsage());

        $this->assertSame(4294967.295, $instance->setEnergyUsage(123456789.123465)->getEnergyUsage());
    }

    public function testEnergyUsageUnit(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setEnergyUsageUnit($value));
        $this->assertSame($value, $instance->getEnergyUsageUnit());
    }

    public function testIdCalculation(): void
    {
        $category1 = new Category();
        $category1->setType('abc')
                  ->setName('def');

        $category2 = new Category();
        $category2->setType('ghi')
                  ->setName('jkl');

        $instance = $this->createInstance();
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
