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
    public function test(): void
    {
        $recipeData = new RecipeData();
        $order = 42;
        $item = new Item();
        $amount = 13.37;

        $instance = new RecipeIngredient();

        $this->assertSame($instance, $instance->setRecipeData($recipeData));
        $this->assertSame($recipeData, $instance->getRecipeData());

        $this->assertSame($instance, $instance->setOrder($order));
        $this->assertSame($order, $instance->getOrder());

        $this->assertSame($instance, $instance->setItem($item));
        $this->assertSame($item, $instance->getItem());

        $this->assertSame($instance, $instance->setAmount($amount));
        $this->assertSame($amount, $instance->getAmount());
    }

    public function testValidation(): void
    {
        $order = 1337;
        $expectedOrder = 255;

        $amount = 123456789.123;
        $expectedAmount = 4294967.295;

        $instance = new RecipeIngredient();

        $this->assertSame($instance, $instance->setOrder($order));
        $this->assertSame($expectedOrder, $instance->getOrder());

        $this->assertSame($instance, $instance->setAmount($amount));
        $this->assertSame($expectedAmount, $instance->getAmount());
    }

    public function testIdCalculation(): void
    {
        $item = new Item();
        $item->setType('abc')
             ->setName('def');

        $instance = new RecipeIngredient();
        $instance->setOrder(42)
                 ->setItem($item)
                 ->setAmount(13.37);

        $expectedId = '3e3db094-bce2-9604-172e-e1ba54e72f78';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
