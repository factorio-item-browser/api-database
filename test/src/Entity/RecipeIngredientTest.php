<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeIngredient class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\RecipeIngredient
 */
class RecipeIngredientTest extends TestCase
{
    private function createInstance(): RecipeIngredient
    {
        return new RecipeIngredient();
    }

    public function testRecipeData(): void
    {
        $value = new RecipeData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setRecipeData($value));
        $this->assertSame($value, $instance->getRecipeData());
    }

    public function testOrder(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setOrder($value));
        $this->assertSame($value, $instance->getOrder());

        $this->assertSame(255, $instance->setOrder(1337)->getOrder());
    }

    public function testItem(): void
    {
        $value = new Item();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setItem($value));
        $this->assertSame($value, $instance->getItem());
    }

    public function testAmount(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAmount($value));
        $this->assertSame($value, $instance->getAmount());

        $this->assertSame(4294967.295, $instance->setAmount(123456789.123465)->getAmount());
    }

    public function testIdCalculation(): void
    {
        $item = new Item();
        $item->setType('abc')
             ->setName('def');

        $instance = $this->createInstance();
        $instance->setOrder(42)
                 ->setItem($item)
                 ->setAmount(13.37);

        $expectedId = '3e3db094-bce2-9604-172e-e1ba54e72f78';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
