<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeProduct class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\RecipeProduct
 */
class RecipeProductTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $recipe = new Recipe('abc', 'def', new CraftingCategory('ghi'));
        $item = new Item('jkl', 'mno');
        $recipeProduct = new RecipeProduct($recipe, $item);

        $this->assertSame($recipe, $recipeProduct->getRecipe());
        $this->assertSame($item, $recipeProduct->getItem());
        $this->assertSame(0., $recipeProduct->getAmountMin());
        $this->assertSame(0., $recipeProduct->getAmountMax());
        $this->assertSame(0., $recipeProduct->getProbability());
        $this->assertSame(0, $recipeProduct->getOrder());
    }

    /**
     * Tests setting and getting the recipe.
     * @covers ::getRecipe
     * @covers ::setRecipe
     */
    public function testSetAndGetRecipe(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );

        $recipe = new Recipe('abc', 'def', new CraftingCategory('ghi'));
        $this->assertSame($recipeProduct, $recipeProduct->setRecipe($recipe));
        $this->assertSame($recipe, $recipeProduct->getRecipe());
    }

    /**
     * Tests setting and getting the item.
     * @covers ::getItem
     * @covers ::setItem
     */
    public function testSetAndGetItem(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );

        $item = new Item('abc', 'def');
        $this->assertSame($recipeProduct, $recipeProduct->setItem($item));
        $this->assertSame($item, $recipeProduct->getItem());
    }

    /**
     * Tests setting and getting the amountMin.
     * @covers ::getAmountMin
     * @covers ::setAmountMin
     */
    public function testSetAndGetAmountMin(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );

        $amountMin = 13.37;
        $this->assertSame($recipeProduct, $recipeProduct->setAmountMin($amountMin));
        $this->assertSame($amountMin, $recipeProduct->getAmountMin());
    }

    /**
     * Tests setting and getting the amountMax.
     * @covers ::getAmountMax
     * @covers ::setAmountMax
     */
    public function testSetAndGetAmountMax(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );

        $amountMax = 13.37;
        $this->assertSame($recipeProduct, $recipeProduct->setAmountMax($amountMax));
        $this->assertSame($amountMax, $recipeProduct->getAmountMax());
    }

    /**
     * Tests the getAmount method.
     * @covers ::getAmount
     */
    public function testGetAmount(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );
        $recipeProduct->setAmountMin(42)
                      ->setAmountMax(21)
                      ->setProbability(0.25);

        $this->assertSame(7.875, $recipeProduct->getAmount());
    }

    /**
     * Tests setting and getting the probability.
     * @covers ::getProbability
     * @covers ::setProbability
     */
    public function testSetAndGetProbability(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );

        $probability = 0.42;
        $this->assertSame($recipeProduct, $recipeProduct->setProbability($probability));
        $this->assertSame($probability, $recipeProduct->getProbability());
    }

    /**
     * Tests setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder(): void
    {
        $recipeProduct = new RecipeProduct(
            new Recipe('foo', 'bar', new CraftingCategory('baz')),
            new Item('bar', 'foo')
        );

        $order = 42;
        $this->assertSame($recipeProduct, $recipeProduct->setOrder($order));
        $this->assertSame($order, $recipeProduct->getOrder());
    }
}
