<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
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

    public function testSetAndGetAmount(): void
    {
        $amount = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAmount($amount));
        $this->assertSame($amount, $instance->getAmount());
    }
}
