<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use PHPUnit\Framework\MockObject\MockObject;
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
     * Tests the setting and getting the recipe.
     * @covers ::getRecipe
     * @covers ::setRecipe
     */
    public function testSetAndGetRecipe(): void
    {
        /* @var Recipe&MockObject $recipe */
        $recipe = $this->createMock(Recipe::class);
        $product = new RecipeProduct();

        $this->assertSame($product, $product->setRecipe($recipe));
        $this->assertSame($recipe, $product->getRecipe());
    }

    /**
     * Tests the setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder(): void
    {
        $order = 42;
        $product = new RecipeProduct();

        $this->assertSame($product, $product->setOrder($order));
        $this->assertSame($order, $product->getOrder());
    }

    /**
     * Tests the setting and getting the item.
     * @covers ::getItem
     * @covers ::setItem
     */
    public function testSetAndGetItem(): void
    {
        /* @var Item&MockObject $item */
        $item = $this->createMock(Item::class);
        $product = new RecipeProduct();

        $this->assertSame($product, $product->setItem($item));
        $this->assertSame($item, $product->getItem());
    }

    /**
     * Tests the setting and getting the amount min.
     * @covers ::getAmountMin
     * @covers ::setAmountMin
     */
    public function testSetAndGetAmountMin(): void
    {
        $amountMin = 13.37;
        $product = new RecipeProduct();

        $this->assertSame($product, $product->setAmountMin($amountMin));
        $this->assertSame($amountMin, $product->getAmountMin());
    }

    /**
     * Tests the setting and getting the amount max.
     * @covers ::getAmountMax
     * @covers ::setAmountMax
     */
    public function testSetAndGetAmountMax(): void
    {
        $amountMax = 13.37;
        $product = new RecipeProduct();

        $this->assertSame($product, $product->setAmountMax($amountMax));
        $this->assertSame($amountMax, $product->getAmountMax());
    }

    /**
     * Tests the setting and getting the probability.
     * @covers ::getProbability
     * @covers ::setProbability
     */
    public function testSetAndGetProbability(): void
    {
        $probability = 13.37;
        $product = new RecipeProduct();

        $this->assertSame($product, $product->setProbability($probability));
        $this->assertSame($probability, $product->getProbability());
    }

    /**
     * Tests the getAmount method.
     * @covers ::getAmount
     */
    public function testGetAmount(): void
    {
        $recipeProduct = new RecipeProduct();
        $recipeProduct->setAmountMin(42)
                      ->setAmountMax(21)
                      ->setProbability(0.25);

        $this->assertSame(7.875, $recipeProduct->getAmount());
    }
}
