<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\TechnologyData;
use FactorioItemBrowser\Api\Database\Entity\TechnologyIngredient;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the TechnologyData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\TechnologyData
 */
class TechnologyDataTest extends TestCase
{
    private function createInstance(): TechnologyData
    {
        return new TechnologyData();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getIngredients());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testCount(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCount($value));
        $this->assertSame($value, $instance->getCount());

        $this->assertSame(4294967295, $instance->setCount(123456789123465)->getCount());
    }

    public function testCountFormula(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCountFormula($value));
        $this->assertSame($value, $instance->getCountFormula());

        $this->assertSame(
            str_repeat('abcde', 51),
            $instance->setCountFormula(str_repeat('abcde', 256))->getCountFormula(),
        );
    }

    public function testTime(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setTime($value));
        $this->assertSame($value, $instance->getTime());

        $this->assertSame(4294967.295, $instance->setTime(123456789.123465)->getTime());
    }

    public function testLevel(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLevel($value));
        $this->assertSame($value, $instance->getLevel());

        $this->assertSame(4294967295, $instance->setLevel(123456789123465)->getLevel());
    }

    public function testMaxLevel(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setMaxLevel($value));
        $this->assertSame($value, $instance->getMaxLevel());

        $this->assertSame(4294967295, $instance->setMaxLevel(123456789123465)->getMaxLevel());
    }

    public function testIdCalculation(): void
    {
        $item1 = new Item();
        $item1->setType('abc')
              ->setName('def');
        $item2 = new Item();
        $item2->setType('ghi')
              ->setName('jkl');

        $ingredient1 = new TechnologyIngredient();
        $ingredient1->setOrder(12)
                    ->setItem($item1);
        $ingredient2 = new TechnologyIngredient();
        $ingredient2->setOrder(23)
                    ->setItem($item2);

        $instance = $this->createInstance();
        $instance->setCount(42)
                 ->setCountFormula('foo')
                 ->setTime(13.37)
                 ->setLevel(1337)
                 ->setMaxLevel(7331);
        $instance->getIngredients()->add($ingredient1);
        $instance->getIngredients()->add($ingredient2);

        $expectedId = 'd7517139-8898-7e93-3198-0363e24189ce';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
