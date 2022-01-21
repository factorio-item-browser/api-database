<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeProduct class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\RecipeProduct
 */
class RecipeProductTest extends TestCase
{
    public function test(): void
    {
        $recipeData = new RecipeData();
        $order = 42;
        $item = new Item();
        $amountMin = 13.37;
        $amountMax = 73.31;
        $probability = 0.21;
        $expectedAmount = 9.101;

        $instance = new RecipeProduct();

        $this->assertSame($instance, $instance->setRecipeData($recipeData));
        $this->assertSame($recipeData, $instance->getRecipeData());

        $this->assertSame($instance, $instance->setOrder($order));
        $this->assertSame($order, $instance->getOrder());

        $this->assertSame($instance, $instance->setItem($item));
        $this->assertSame($item, $instance->getItem());

        $this->assertSame($instance, $instance->setAmountMin($amountMin));
        $this->assertSame($amountMin, $instance->getAmountMin());

        $this->assertSame($instance, $instance->setAmountMax($amountMax));
        $this->assertSame($amountMax, $instance->getAmountMax());

        $this->assertSame($instance, $instance->setProbability($probability));
        $this->assertSame($probability, $instance->getProbability());

        $this->assertSame($expectedAmount, $instance->getAmount());
    }

    public function testValidation(): void
    {
        $order = 1337;
        $expectedOrder = 255;
        $amountMin = 123456789.123465;
        $expectedAmountMin = 4294967.295;
        $amountMax = 987654321.987654;
        $expectedAmountMax = 4294967.295;
        $probability = 0.123456789;
        $expectedProbability = 0.123;

        $instance = new RecipeProduct();

        $this->assertSame($instance, $instance->setOrder($order));
        $this->assertSame($expectedOrder, $instance->getOrder());

        $this->assertSame($instance, $instance->setAmountMin($amountMin));
        $this->assertSame($expectedAmountMin, $instance->getAmountMin());

        $this->assertSame($instance, $instance->setAmountMax($amountMax));
        $this->assertSame($expectedAmountMax, $instance->getAmountMax());

        $this->assertSame($instance, $instance->setProbability($probability));
        $this->assertSame($expectedProbability, $instance->getProbability());
    }

    public function testIdCalculation(): void
    {
        $item = new Item();
        $item->setType('abc')
             ->setName('def');

        $instance = new RecipeProduct();
        $instance->setOrder(42)
                 ->setItem($item)
                 ->setAmountMin(13.37)
                 ->setAmountMax(73.31)
                 ->setProbability(0.21);

        $expectedId = '13dfacfe-67ee-a30d-9b9f-c009893448c7';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
