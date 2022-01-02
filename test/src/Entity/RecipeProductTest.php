<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
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
    private function createInstance(): RecipeProduct
    {
        return new RecipeProduct();
    }

    public function testSetAndGetRecipe(): void
    {
        $recipe = new Recipe();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setRecipe($recipe));
        $this->assertSame($recipe, $instance->getRecipe());
    }

    public function testSetAndGetOrder(): void
    {
        $order = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setOrder($order));
        $this->assertSame($order, $instance->getOrder());
    }

    public function testSetAndGetItem(): void
    {
        $item = new Item();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setItem($item));
        $this->assertSame($item, $instance->getItem());
    }

    public function testSetAndGetAmountMin(): void
    {
        $amountMin = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAmountMin($amountMin));
        $this->assertSame($amountMin, $instance->getAmountMin());
    }

    public function testSetAndGetAmountMax(): void
    {
        $amountMax = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAmountMax($amountMax));
        $this->assertSame($amountMax, $instance->getAmountMax());
    }

    public function testSetAndGetProbability(): void
    {
        $probability = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setProbability($probability));
        $this->assertSame($probability, $instance->getProbability());
    }

    public function testGetAmount(): void
    {
        $recipeProduct = $this->createInstance();
        $recipeProduct->setAmountMin(42)
                      ->setAmountMax(21)
                      ->setProbability(0.25);

        $this->assertSame(7.875, $recipeProduct->getAmount());
    }
}
